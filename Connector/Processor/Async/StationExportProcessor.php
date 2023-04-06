<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Connector\Processor\Async;

use Dnd\Bundle\DpdFranceShippingBundle\Async\Topics;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\ExportException;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\PackageException;
use Dnd\Bundle\DpdFranceShippingBundle\Factory\PackageFactory;
use Dnd\Bundle\DpdFranceShippingBundle\Method\DpdFranceShippingMethod;
use Dnd\Bundle\DpdFranceShippingBundle\Model\DpdShippingPackageOptionsInterface;
use Dnd\Bundle\DpdFranceShippingBundle\Normalizer\OrderNormalizer;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\SettingsProvider;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\ShippingServiceProvider;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\InvalidArgumentException;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\OrderBundle\Converter\OrderShippingLineItemConverterInterface;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\OrderBundle\Entity\OrderShippingTracking;
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

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class StationExportProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    /**
     * The local FS folder for the files
     */
    public const LOCAL_FOLDER = 'data/dpd-france/export/';
    /**
     * The distant folder for the files
     */
    public const TARGET_FOLDER = '/export/';
    /**
     * The local FS sub folder for the files successfully exported to station
     */
    public const SUCCESS_FOLDER = 'success/';
    /**
     * The local FS sub folder for the files that were NOT exported to station
     */
    public const FAIL_FOLDER = 'error/';
    /**
     * The prefix for the station filenames
     */
    public const FILE_PREFIX = 'oro';
    /**
     * The extension for the station filenames
     */
    public const FILE_EXTENSION = 'txt';

    protected Filesystem $filesystem;
    protected ?ParameterBag $settings = null;

    /**
     * @var DpdShippingPackageOptionsInterface[]|null $packages
     */
    private ?array $packages = null;

    public function __construct(
        private readonly DoctrineHelper $doctrineHelper,
        private readonly OrderNormalizer $normalizer,
        private readonly SettingsProvider $settingsProvider,
        private readonly LoggerInterface $logger,
        private readonly ShippingServiceProvider $shippingServiceProvider,
        private readonly OrderShippingLineItemConverterInterface $shippingLineItemConverter,
        private readonly PackageFactory $packagesFactory,
        private readonly SymmetricCrypterInterface $crypter
    ) {
        $this->filesystem = new SymfonyFileSystem();
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
        $topic = $message->getProperty(Config::PARAMETER_TOPIC_NAME);

        $body = JSON::decode($message->getBody());
        if (empty($body['orderId'])) {
            $this->logger->error(
                sprintf(
                    '[Station export]: Incomplete message, the body must contain an orderId. Topic: %s - Body: %s',
                    $body['orderId'],
                    $topic
                )
            );

            return self::REJECT;
        }
        $order = $this->getManager()->find(Order::class, $body['orderId']);
        if ($order === null) {
            $this->logger->error(
                sprintf('[Station export] Async processor could not fetch order with id %d for topic %s', $body['orderId'], $topic)
            );

            return self::REJECT;
        }
        if ($topic === Topics::SHIPMENT_EXPORT_TO_DPD_STATION && $order->getSynchronizedDpd() !== null) {
            $this->logger->warning(
                sprintf('[Station export] Skipping already exported order with id %d.', $body['orderId'])
            );

            return self::REJECT;
        }


        return $this->processAsync($order, $topic);
    }

    /**
     * Creates a local station file for the order and copies it to station FTP
     */
    public function processAsync(Order $order, string $topic): string
    {
        try {
            $fileName = $this->generateFileName($order->getId(), $topic);

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
                    $this->normalizer->normalize($order, 'dpd_fr_station', [
                        'shipping_service' => $shippingService,
                        'settings' => $this->getSettings(),
                        'packages' => $this->getPackages($order, $shippingService),
                    ])
                )
            );

            if (!$this->filesystem->exists($this->getStationFtpUrl(self::TARGET_FOLDER))) {
                $this->filesystem->mkdir($this->getStationFtpUrl(self::TARGET_FOLDER));
            }
            $this->filesystem->copy(
                self::LOCAL_FOLDER . $fileName,
                $this->getStationFtpUrl(self::TARGET_FOLDER . $fileName)
            );

            $this->filesystem->copy(
                self::LOCAL_FOLDER . $fileName,
                self::LOCAL_FOLDER . self::SUCCESS_FOLDER . $fileName
            );
            $this->filesystem->remove(self::LOCAL_FOLDER . $fileName);

            $this->onSuccess($order, $this->getSettings());
        } catch (\Throwable $e) {
            $errorMsg = 'DPD France export processor failed to export shipment to station';
            $this->logError($errorMsg, $order, $topic, $e);
            $this->onFail($fileName);

            return MessageProcessorInterface::REJECT;
        }

        return MessageProcessorInterface::ACK;
    }

    /**
     * Returns the FTP url for the target file
     */
    private function getStationFtpUrl(string $targetPath): string
    {
        $this->settings = $this->getSettings();
        $host = $this->settings->get('dpd_fr_station_ftp_host');
        $port = $this->settings->getInt('dpd_fr_station_ftp_port');
        $username = $this->settings->get('dpd_fr_station_ftp_user');
        $password = $this->crypter->decryptData($this->settings->get('dpd_fr_station_ftp_password'));
        if (!isset($host, $port, $username, $password)) {
            throw new \InvalidArgumentException('At least one parameter is missing for FTP connection.');
        }

        return sprintf("ftp://%s:%s@%s:%d%s", $username, $password, $host, $port, $targetPath);
    }

    /**
     * Gets the different packages needed to ship the order
     *
     * @return DpdShippingPackageOptionsInterface[]|null
     * @throws PackageException
     */
    private function getPackages(Order $order, ShippingService $shippingService): ?array
    {
        if ($this->packages === null) {
            $convertedLineItems = $this->shippingLineItemConverter->convertLineItems($order->getLineItems());
            if ($convertedLineItems === null) {
                throw new InvalidArgumentException('The order does not contain any line item.');
            }
            $this->packages = $this->packagesFactory->create(
                $convertedLineItems,
                $shippingService,
                $order->getWebsite()->getId()
            );
        }

        return $this->packages;
    }

    /**
     * Assembles the string ready to be dumped into the file
     *
     * @throws ExportException
     */
    private function assembleNormalizedData(array $data): string
    {
        $mightyString = $this->getFileHeader();
        $lineString = '';

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
                $lineString = '';
            }
        }

        return $mightyString;
    }

    /**
     * Prepares the first line of the file
     */
    private function getFileHeader(): string
    {
        return '$VERSION=110' . PHP_EOL;
    }

    /**
     * Generates the filename for the export
     */
    private function generateFileName(int $orderId, string $topic): string
    {
        $now = new \DateTime();

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
     */
    private function logError(string $errorMsg, Order $order, string $topic, ?\Throwable $e = null): void
    {
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
     */
    public function onSuccess(Order $order, ParameterBag $settings): void
    {
        $order->setSynchronizedDpd(new \DateTime());

        foreach ($this->getTrackingLinks($order, $settings) as $trackingLink) {
            $shippingTracking = new OrderShippingTracking();
            $shippingTracking->setMethod($order->getShippingMethod());
            $shippingTracking->setNumber($trackingLink);
            $order->addShippingTracking($shippingTracking);
        }
        $this->getManager()->persist($order);
        $this->getManager()->flush();
    }

    /**
     * Description getTrackingLinks function
     *
     * @return string[]
     */
    private function getTrackingLinks(Order $order, ParameterBag $settings): array
    {
        /** @var string[] $trackingLinks */
        $trackingLinks = [];
        $pkgAmount = count($this->packages);
        for ($i = 0; $i < $pkgAmount; $i++) {
            //'%s://www.dpd.fr/tracer_%s_%d%d';
            $trackingLinks[] = sprintf(
                DpdFranceShippingMethod::TRACKING_URL_PATTERN,
                'https',
                $order->getId() . '-' . $i > 0 ? $i : '',
                (int)$settings->get('dpd_fr_agency_code'),
                (int)$settings->get('dpd_fr_contract_number')
            );
        }

        return $trackingLinks;
    }

    /**
     * Gets the entity manager of the exported entity
     */
    public function getManager(): EntityManagerInterface
    {
        return $this->doctrineHelper->getEntityManagerForClass(Order::class);
    }

    /**
     * Returns the settings from DPD France Integration
     */
    private function getSettings(): ParameterBag
    {
        if (null === $this->settings) {
            $this->settings = $this->settingsProvider->getSettings();
        }

        return $this->settings;
    }
}
