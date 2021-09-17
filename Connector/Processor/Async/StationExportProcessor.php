<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Connector\Processor\Async;

use Dnd\Bundle\DpdFranceShippingBundle\Async\Topics;
use Dnd\Bundle\DpdFranceShippingBundle\Connector\Client\FTPClient;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\ExportException;
use Dnd\Bundle\DpdFranceShippingBundle\Normalizer\OrderNormalizer;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\SettingsProvider;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\ShippingServiceProvider;
use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\SecurityBundle\Encoder\SymmetricCrypterInterface;
use Oro\Component\MessageQueue\Client\Config;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Util\JSON;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class StationExportProcessor
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Connector\Processor\Async
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class StationExportProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    /**
     * The local FS folder for the files
     *
     * @var string LOCAL_FOLDER
     */
    public const LOCAL_FOLDER = 'data/dpd-france/export/';
    /**
     * The local FS sub folder for the files successfully exported to station
     *
     * @var string SUCCESS_FOLDER
     */
    public const SUCCESS_FOLDER = 'success/';
    /**
     * The local FS sub folder for the files that were NOT exported to station
     *
     * @var string FAIL_FOLDER
     */
    public const FAIL_FOLDER = 'error/';
    /**
     * The prefix for the station filenames
     *
     * @var string FILE_PREFIX
     */
    public const FILE_PREFIX = 'oro';
    /**
     * The extension for the station filenames
     *
     * @var string FILE_EXTENSION
     */
    public const FILE_EXTENSION = 'txt';
    /**
     * Description $doctrineHelper field
     *
     * @var DoctrineHelper $doctrineHelper
     */
    protected DoctrineHelper $doctrineHelper;
    /**
     * The normalizer for the exported entity
     *
     * @var NormalizerInterface $normalizer
     */
    protected NormalizerInterface $normalizer;
    /**
     * Description $logger field
     *
     * @var LoggerInterface $logger
     */
    protected LoggerInterface $logger;
    /**
     * Description $filesystem field
     *
     * @var Filesystem $filesystem
     */
    protected Filesystem $filesystem;
    /**
     * Description $FTPClient field
     *
     * @var FTPClient|null $FTPClient
     */
    protected ?FTPClient $FTPClient = null;
    /**
     * Description $settingsProvider field
     *
     * @var SettingsProvider $settingsProvider
     */
    protected SettingsProvider $settingsProvider;
    /**
     * Description $shippingServiceProvider field
     *
     * @var ShippingServiceProvider $shippingServiceProvider
     */
    protected ShippingServiceProvider $shippingServiceProvider;
    /**
     * Description $settings field
     *
     * @var ParameterBag|null $settings
     */
    protected ?ParameterBag $settings = null;
    /**
     * Description $crypter field
     *
     * @var SymmetricCrypterInterface $crypter
     */
    protected SymmetricCrypterInterface $crypter;

    /**
     * AbstractExportProcessor constructor
     *
     * @param DoctrineHelper            $doctrineHelper
     * @param OrderNormalizer           $normalizer
     * @param SettingsProvider          $settingsProvider
     * @param LoggerInterface           $logger
     * @param ShippingServiceProvider   $shippingServiceProvider
     * @param SymmetricCrypterInterface $crypter
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        OrderNormalizer $normalizer,
        SettingsProvider $settingsProvider,
        LoggerInterface $logger,
        ShippingServiceProvider $shippingServiceProvider,
        SymmetricCrypterInterface $crypter
    ) {
        $this->doctrineHelper          = $doctrineHelper;
        $this->normalizer              = $normalizer;
        $this->logger                  = $logger;
        $this->filesystem              = new SymfonyFileSystem();
        $this->settingsProvider        = $settingsProvider;
        $this->shippingServiceProvider = $shippingServiceProvider;
        $this->crypter = $crypter;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedTopics(): array
    {
        return [
            Topics::SHIPMENT_EXPORT_TO_DPD_STATION,
            Topics::SHIPMENT_EXPORT_TO_DPD_STATION_FORCED,
        ];
    }

    /**
     * Processes order export
     *
     * {@inheritdoc}
     */
    public function process(MessageInterface $message, SessionInterface $session): string
    {
        /** @var string $topic */
        $topic = $message->getProperty(Config::PARAMETER_TOPIC_NAME);

        /** @var array|null $body */
        $body = JSON::decode($message->getBody());
        if (empty($body['orderId'])) {
            $this->logger->error(
                sprintf(
                    'Incomplete message, the body must contain an orderId. Topic: %s - Body: %s',
                    $body['orderId'],
                    $topic
                )
            );

            return self::REJECT;
        }
        /** @var Order $order */
        $order = $this->getManager()->find(Order::class, $body['orderId']);

        if ($order === null) {
            $this->logger->error(
                sprintf('Async processor could not fetch order with id %d for topic %s', $body['orderId'], $topic)
            );

            return self::REJECT;
        }

        return $this->processAsync($order, $topic);
    }

    /**
     * Creates a local station file for the order and copies it to station FTP
     *
     * @param Order  $order the exported entity
     * @param string $topic the async message topic
     *
     * @return string
     */
    public function processAsync(Order $order, string $topic): string
    {
        try {
            /** @var string $fileName */
            $fileName = $this->generateFileName($order->getId(), $topic);

            /** @var ShippingService $shippingService */
            $shippingService = $this->shippingServiceProvider->getServiceForMethodTypeIdentifier(
                $order->getShippingMethodType()
            );

            if (!$this->filesystem->exists(self::LOCAL_FOLDER)) {
                $this->filesystem->mkdir(self::LOCAL_FOLDER);
                $this->filesystem->mkdir(self::LOCAL_FOLDER . self::SUCCESS_FOLDER);
                $this->filesystem->mkdir(self::LOCAL_FOLDER . self::FAIL_FOLDER);
            }
            $this->filesystem->touch(self::LOCAL_FOLDER . $fileName);
            $this->filesystem->dumpFile(
                self::LOCAL_FOLDER . $fileName,
                $this->assembleNormalizedData(
                    $this->normalizer->normalize($order,
                        'dpd_fr_station',
                        ['shipping_service' => $shippingService, 'settings' => $this->getSettings()])
                )
            );

            /** @var FTPClient $FTPClient */
            $FTPClient = $this->getFTPClient();
            if ($FTPClient === null || !$FTPClient->putFile($fileName, self::LOCAL_FOLDER . $fileName)) {
                $this->onFail($fileName);
                throw new \Exception('Fail to upload exported file to SFTP');
            }

            $this->filesystem->copy(
                self::LOCAL_FOLDER . $fileName,
                self::LOCAL_FOLDER . self::SUCCESS_FOLDER . $fileName
            );
            $this->filesystem->remove(self::LOCAL_FOLDER . $fileName);

            $this->onSuccess($order);
        } catch (\Throwable $e) {
            /** @var string $errorMsg */
            $errorMsg = 'DPD France export processor failed to export shipment to station';
            $this->logError($errorMsg, $order, $topic, $e);
            $this->onFail($fileName);

            return MessageProcessorInterface::REJECT;
        }

        return MessageProcessorInterface::ACK;
    }

    /**
     * Assembles the string ready to be dumped into the file
     *
     * @param mixed[] $data
     *
     * @return string
     * @throws ExportException
     */
    private function assembleNormalizedData(array $data): string
    {
        /** @var string $mightyString */
        $mightyString = $this->getFileHeader();
        /** @var string $lineString */
        $lineString = '';

        /** @var mixed[] $datum */
        foreach ($data as $datum) {
            if (strlen($lineString) + 1 !== $datum['position']) {
                throw new ExportException(
                    sprintf(
                        'Data inconsistency, attribute %s not in its rightful position. (%d instead of %d)',
                        $datum['code'],
                        $datum['position'],
                        strlen($lineString) + 1,
                    )
                );
            }
            $lineString .= $datum['value'];
            if ($datum['position'] === 2247) {
                $mightyString .= $lineString;
                $lineString   = '';
            }
        }

        return $mightyString;
    }

    /**
     * Prepares the first line of the file
     *
     * @return void
     */
    private function getFileHeader(): string
    {
        return '$VERSION=110' . PHP_EOL;
    }

    /**
     * Generates the filename for the export
     *
     * @param int    $orderId
     * @param string $topic
     *
     * @return string
     */
    private function generateFileName(int $orderId, string $topic): string
    {
        /** @var \DateTime $now */
        $now = new \DateTime();

        /** @var string $forced */
        $forced = $topic === Topics::SHIPMENT_EXPORT_TO_DPD_STATION_FORCED ? '_forced' : '';

        return sprintf(
            '%s_%s_%s%s.%s',
            self::FILE_PREFIX,
            $now->format('Ymd_His'),
            (string)$orderId,
            $forced,
            self::FILE_EXTENSION
        );
    }

    /**
     * Prepares an error message with some details about the context and logs it
     *
     * @param string          $errorMsg Some information about the exception
     * @param Order           $order the order being exported
     * @param string          $topic the topic of the async message
     * @param \Throwable|null $e
     *
     * @return void
     */
    private function logError(string $errorMsg, Order $order, string $topic, ?\Throwable $e = null): void
    {
        /** @var string $entityInfoMsg */
        $entityInfoMsg = sprintf(
            ' while exporting order with id %d for topic %s. ',
            $order->getId(),
            $topic
        );
        $this->logger->error($errorMsg . $entityInfoMsg);
        if ($e !== null) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Does some cleanup and logging whenever the process fails
     *
     * @param string|null $fileName
     *
     * @return void
     */
    public function onFail(?string $fileName): void
    {
        if ($fileName !== null) {
            try {
                $this->filesystem->copy(
                    self::LOCAL_FOLDER . $fileName,
                    self::LOCAL_FOLDER . self::FAIL_FOLDER . $fileName
                );
                $this->filesystem->remove(self::LOCAL_FOLDER . $fileName);
            } catch (\Exception $e) {
                $this->logger->error('Failed to move file to local error folder');
                $this->logger->error($e->getMessage());
            }
        }
    }

    /**
     * Flag the exported order as having its shipment successfully been synchronized with DPD Station
     *
     * @param Order $order the order being exported
     *
     * @return void
     */
    public function onSuccess(Order $order): void
    {
        $order->setSynchronizedDpd(new \DateTime());
        $this->getManager()->persist($order);
        $this->getManager()->flush();
    }

    /**
     * Gets the entity manager of the exported entity
     *
     * @return EntityManagerInterface
     */
    public function getManager(): EntityManagerInterface
    {
        return $this->doctrineHelper->getEntityManagerForClass(Order::class);
    }

    /**
     * Returns a configured FTP client
     *
     * @return FTPClient|null
     */
    private function getFTPClient(): ?FTPClient
    {
        if (null === $this->FTPClient) {
            $this->FTPClient = new FTPClient($this->getSettings(), $this->crypter);
        }

        return $this->FTPClient;
    }

    /**
     * Returns the settings from DPD France Integration
     *
     * @return ParameterBag
     */
    private function getSettings(): ParameterBag
    {
        if (null === $this->settings) {
            $this->settings = $this->settingsProvider->getSettings();
        }

        return $this->settings;
    }
}
