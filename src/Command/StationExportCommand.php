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
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use http\Exception\InvalidArgumentException;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\OrderBundle\Converter\OrderShippingLineItemConverterInterface;
use Oro\Bundle\OrderBundle\Entity\Order;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
#[\Symfony\Component\Console\Attribute\AsCommand('dnd:dpd:station-export:test', 'Helps debugging station export by outputing the export data for a given order.')]
class StationExportCommand extends Command
{
    protected ?ParameterBag $settings = null;
    protected StationExportProcessor $stationExportProcessor;
    /**
     * @var DpdShippingPackageOptionsInterface[]|null $packages
     */
    private ?array $packages = null;

    public function __construct(
        private readonly DoctrineHelper $doctrineHelper,
        private readonly ShippingServiceProvider $shippingServiceProvider,
        private readonly OrderNormalizer $normalizer,
        private readonly SettingsProvider $settingsProvider,
        private readonly OrderShippingLineItemConverterInterface $shippingLineItemConverter,
        private readonly PackageFactory $packagesFactory
    ) {
        parent::__construct();
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function configure(): void
    {
        $this
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $orderId = $input->getArgument('id');

        $orderManager = $this->doctrineHelper->getEntityManagerForClass(Order::class);

        try {
            $order = $orderManager->find(Order::class, (string)$orderId);
        } catch (OptimisticLockException | TransactionRequiredException | ORMException $e) {
            $output->writeln('Could not load requested order');
            $output->write($e->getMessage());
        }

        if (!$order instanceof Order) {
            $output->writeln('Could not find requested order');

            return \Symfony\Component\Console\Command\Command::SUCCESS;
        }
        $shippingService = $this->shippingServiceProvider->getServiceForMethodTypeIdentifier(
            $order->getShippingMethodType()
        );
        try {
            /** @var string[] $data */
            $data = $this->normalizer->normalize($order, 'dpd_fr_station', [
                'shipping_service' => $shippingService,
                'settings' => $this->getSettings(),
                'packages' => $this->getPackages($order, $shippingService),
            ]);
        } catch (NormalizerException | ExceptionInterface | PackageException $e) {
            $output->writeln('Something wrong happened with the order.');
            $output->write($e->getMessage());

            return \Symfony\Component\Console\Command\Command::SUCCESS;
        }

        $output->writeln(
            sprintf(
                'The order should be sent in %d package%s.',
                count($this->packages),
                count($this->packages) > 1 ? 's' : ''
            )
        );
        $table = new Table($output);
        $table->setHeaders(['code', 'position', 'length', 'value'])->setRows($data);
        $table->render();

        return \Symfony\Component\Console\Command\Command::SUCCESS;
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
}
