<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Command;

use Dnd\Bundle\DpdFranceShippingBundle\Connector\Processor\Async\StationExportProcessor;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\NormalizerException;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\PackageException;
use Dnd\Bundle\DpdFranceShippingBundle\Factory\PackageFactory;
use Dnd\Bundle\DpdFranceShippingBundle\Model\DpdShippingPackageOptionsInterface;
use Dnd\Bundle\DpdFranceShippingBundle\Normalizer\OrderNormalizer;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\SettingsProvider;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\ShippingServiceProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use http\Exception\InvalidArgumentException;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\OrderBundle\Converter\OrderShippingLineItemConverterInterface;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\ShippingBundle\Context\LineItem\Collection\ShippingLineItemCollectionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * Class StationExportCommand
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Command
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class StationExportCommand extends Command
{
    /**
     * Description $settings field
     *
     * @var ParameterBag|null $settings
     */
    protected ?ParameterBag $settings = null;
    /** @var string */
    protected static $defaultName = 'dnd:dpd:station-export:test';
    /**
     * Description $stationExportProcessor field
     *
     * @var StationExportProcessor $stationExportProcessor
     */
    protected StationExportProcessor $stationExportProcessor;
    /**
     * Description $doctrineHelper field
     *
     * @var DoctrineHelper $doctrineHelper
     */
    protected DoctrineHelper $doctrineHelper;
    /**
     * Description $shippingServiceProvider field
     *
     * @var ShippingServiceProvider $shippingServiceProvider
     */
    protected ShippingServiceProvider $shippingServiceProvider;
    /**
     * Description $normalizer field
     *
     * @var OrderNormalizer $normalizer
     */
    protected OrderNormalizer $normalizer;
    /**
     * Description $settingsProvider field
     *
     * @var SettingsProvider $settingsProvider
     */
    protected SettingsProvider $settingsProvider;
    /**
     * Description $shippingLineItemConverter field
     *
     * @var OrderShippingLineItemConverterInterface $shippingLineItemConverter
     */
    protected OrderShippingLineItemConverterInterface $shippingLineItemConverter;
    /**
     * Description $packagesFactory field
     *
     * @var PackageFactory $packagesFactory
     */
    protected PackageFactory $packagesFactory;
    /**
     * Description $packages field
     *
     * @var DpdShippingPackageOptionsInterface[]|null $packages
     */
    private ?array $packages = null;

    /**
     * StationExportCommand constructor
     *
     * @param DoctrineHelper                          $doctrineHelper
     * @param ShippingServiceProvider                 $shippingServiceProvider
     * @param OrderNormalizer                         $normalizer
     * @param SettingsProvider                        $settingsProvider
     * @param OrderShippingLineItemConverterInterface $shippingLineItemConverter
     * @param PackageFactory                          $packagesFactory
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        ShippingServiceProvider $shippingServiceProvider,
        OrderNormalizer $normalizer,
        SettingsProvider $settingsProvider,
        OrderShippingLineItemConverterInterface $shippingLineItemConverter,
        PackageFactory $packagesFactory
    ) {
        parent::__construct();
        $this->doctrineHelper            = $doctrineHelper;
        $this->shippingServiceProvider   = $shippingServiceProvider;
        $this->normalizer                = $normalizer;
        $this->settingsProvider          = $settingsProvider;
        $this->shippingLineItemConverter = $shippingLineItemConverter;
        $this->packagesFactory           = $packagesFactory;
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function configure(): void
    {
        $this->setDescription('Helps debugging station export by outputing the export data for a given order.')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Order ID'
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command helps you test Station export.

  <info>php %command.full_name%</info>

HELP
            );
    }

    /**
     * Description execute function
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var mixed $orderId */
        $orderId = $input->getArgument('id');

        /** @var EntityManager $orderManager */
        $orderManager = $this->doctrineHelper->getEntityManagerForClass(Order::class);

        try {
            /** @var Order $order */
            $order = $orderManager->find(Order::class, (string)$orderId);
        } catch (OptimisticLockException | TransactionRequiredException | ORMException $e) {
            $output->writeln('Could not load requested order');
            $output->write($e->getMessage());
        }

        if (!$order instanceof Order) {
            $output->writeln('Could not find requested order');

            return 0;
        }
        /** @var ShippingService $shippingService */
        $shippingService = $this->shippingServiceProvider->getServiceForMethodTypeIdentifier(
            $order->getShippingMethodType()
        );
        try {
            /** @var string[] $data */
            $data = $this->normalizer->normalize($order, 'dpd_fr_station', [
                'shipping_service' => $shippingService,
                'settings'         => $this->getSettings(),
                'packages'         => $this->getPackages($order, $shippingService),
            ]);
        } catch (NormalizerException | ExceptionInterface | PackageException $e) {
            $output->writeln('Something wrong happened with the order.');
            $output->write($e->getMessage());

            return 0;
        }

        $output->writeln(
            sprintf('The order should be sent in %d package%s.',
                count($this->packages),
                count($this->packages) > 1 ? 's' : ''
            )
        );
        /** @var Table $table */
        $table = new Table($output);
        $table->setHeaders(['code', 'position', 'length', 'value'])->setRows($data);
        $table->render();
        return 0;
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

    /**
     * Gets the different packages needed to ship the order
     *
     * @param Order           $order
     * @param ShippingService $shippingService
     *
     * @return DpdShippingPackageOptionsInterface[]|null
     * @throws PackageException
     */
    private function getPackages(Order $order, ShippingService $shippingService): ?array
    {
        if ($this->packages === null) {
            /** @var ShippingLineItemCollectionInterface|null $convertedLineItems */
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
}
