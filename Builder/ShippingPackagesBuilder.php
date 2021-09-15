<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Builder;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\PackageException;
use Dnd\Bundle\DpdFranceShippingBundle\Factory\DpdShippingPackageOptionsFactoryInterface;
use Dnd\Bundle\DpdFranceShippingBundle\Model\DpdShippingPackageOptions;
use Dnd\Bundle\DpdFranceShippingBundle\Model\DpdShippingPackageOptionsInterface;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\SettingsProvider;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Context\ShippingLineItemInterface;
use Oro\Bundle\ShippingBundle\Entity\LengthUnit;
use Oro\Bundle\ShippingBundle\Entity\WeightUnit;
use Oro\Bundle\ShippingBundle\Model\Dimensions;
use Oro\Bundle\ShippingBundle\Model\ShippingPackageOptionsInterface;
use Oro\Bundle\ShippingBundle\Model\Weight;
use Oro\Bundle\ShippingBundle\Provider\MeasureUnitConversion;

/**
 * Class ShippingPackagesBuilder
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Builder
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShippingPackagesBuilder
{
    /**
     * DPD France length unit used for limitations
     *
     * @var string LENGTH_UNIT
     */
    public const LENGTH_UNIT = 'm';
    /**
     * DPD France weight unit used for limitations
     *
     * @var string WEIGHT_UNIT
     */
    public const WEIGHT_UNIT = 'kg';
    /**
     * DPD France currency used for limitations
     *
     * @var string CURRENCY
     */
    public const CURRENCY = 'EUR';
    /**
     * Description $measureUnitConversion field
     *
     * @var MeasureUnitConversion $measureUnitConversion
     */
    protected MeasureUnitConversion $measureUnitConversion;
    /**
     * Description $settingsProvider field
     *
     * @var SettingsProvider $settingsProvider
     */
    protected SettingsProvider $settingsProvider;
    /**
     * @var DpdShippingPackageOptionsFactoryInterface
     */
    private DpdShippingPackageOptionsFactoryInterface $packageOptionsFactory;
    /**
     * Description $currentPackage field
     *
     * @var DpdShippingPackageOptions|null $currentPackage
     */
    private ?DpdShippingPackageOptions $currentPackage = null;
    /**
     * @var DpdShippingPackageOptionsInterface[]
     */
    private array $packages = [];
    /**
     * Description $shippingService field
     *
     * @var ShippingService $shippingService
     */
    private ShippingService $shippingService;

    /**
     * ShippingPackagesBuilder constructor
     *
     * @param DpdShippingPackageOptionsFactoryInterface $packageOptionsFactory
     * @param MeasureUnitConversion                     $measureUnitConversion
     * @param SettingsProvider                          $settingsProvider
     */
    public function __construct(
        DpdShippingPackageOptionsFactoryInterface $packageOptionsFactory,
        MeasureUnitConversion $measureUnitConversion,
        SettingsProvider $settingsProvider
    ) {
        $this->packageOptionsFactory = $packageOptionsFactory;
        $this->measureUnitConversion = $measureUnitConversion;
        $this->settingsProvider      = $settingsProvider;
    }

    /**
     * Init the builder for each shipping service
     *
     * @param ShippingService $shippingService
     *
     * @return void
     */
    public function init(ShippingService $shippingService): void
    {
        $this->resetCurrentPackage();
        $this->packages        = [];
        $this->shippingService = $shippingService;
    }

    /**
     * Adds the given lineItem into the virtual pile of boxes
     *
     * @param ShippingLineItemInterface $lineItem
     *
     * @return bool
     * @throws PackageException
     */
    public function addLineItem(ShippingLineItemInterface $lineItem): bool
    {
        if (!$lineItem->getWeight()) {
            $this->badItemException($lineItem, 'Unknown weight.');
        }
        if (!$lineItem->getDimensions()) {
            $this->badItemException($lineItem, 'Unknown size.');
        }
        /** @var Weight $weight */
        $weight = $this->measureUnitConversion->convert($lineItem->getWeight(), self::WEIGHT_UNIT);
        /** @var Dimensions $dimensions */
        $dimensions = $this->measureUnitConversion->convert($lineItem->getDimensions(), self::LENGTH_UNIT);

        if ($weight === null) {
            $this->badItemException($lineItem, sprintf('Could not convert the weight into %s.', self::WEIGHT_UNIT));
        }
        if (!$lineItem->getDimensions()) {
            $this->badItemException($lineItem, sprintf('Could not convert the dimensions into %s.', self::LENGTH_UNIT));
        }

        /** @var DpdShippingPackageOptionsInterface $itemOptions */
        $itemOptions = $this->packageOptionsFactory->create($dimensions, $weight, $lineItem->getPrice());

        if (!$this->itemCanFit($itemOptions)) {
            $this->badItemException($lineItem, 'Either too heavy, too big or too pricey.');
        }
        for ($i = 0; $i < $lineItem->getQuantity(); $i++) {
            if (!$this->itemCanFitInCurrentPackage($itemOptions)) {
                if (!$this->packCurrentPackage()) {
                    throw new PackageException(
                        sprintf(
                            'Too many packages, need more than %d packages while %d are allowed. Advise splitting order.',
                            count($this->packages) + 1,
                            $this->getParcelMaxAmount()
                        )
                    );
                }
                $this->resetCurrentPackage();
            }
            $this->currentPackage = $this->addItemToPackage($this->currentPackage, $itemOptions);
        }

        return true;
    }

    /**
     * Retrieves the max amount of parcel per shipment for the current service
     *
     * @return int
     */
    private function getParcelMaxAmount(): int
    {
        /** @var int|null $maxAmount */
        $maxAmount = $this->shippingService->getParcelMaxAmount();
        if ($maxAmount === null) {
            //Fallback on general value set at integration level
            $maxAmount = $this->settingsProvider->getSettings()->get('max_amount');
        }
        return $maxAmount;
    }

    /**
     * Helper to throw a PackageException for a given lineItem
     *
     * @param ShippingLineItemInterface $lineItem
     * @param string|null               $message
     *
     * @return void
     * @throws PackageException
     */
    private function badItemException(ShippingLineItemInterface $lineItem, ?string $message = ''): void
    {
        throw new PackageException(
            sprintf(
                'The item %s (%s) cannot be shipped with DPD FR. %s',
                $lineItem->getProduct()->getName(),
                $lineItem->getProduct()->getSku(),
                $message
            )
        );
    }

    /**
     * Is the package light and small enough to be shipped with the specified Dpd France service ?
     *
     * @param DpdShippingPackageOptionsInterface $itemOptions
     *
     * @return bool
     */
    private function itemCanFit(DpdShippingPackageOptionsInterface $itemOptions): bool
    {
        return (
            $itemOptions->getLength() < $this->shippingService->getParcelMaxLength()    &&
            $itemOptions->getWidth()  < $this->shippingService->getParcelMaxLength()    &&
            $itemOptions->getWeight() < $this->shippingService->getParcelMaxWeight()    &&
            $itemOptions->getGirth()  < $this->shippingService->getParcelMaxPerimeter() &&
            $itemOptions->getPrice()->getValue() < $this->shippingService->getParcelMaxValue()
        );
    }

    /**
     * Can the item be squeezed into the current package ?
     *
     * @param DpdShippingPackageOptionsInterface $itemOptions
     *
     * @return bool
     */
    private function itemCanFitInCurrentPackage(DpdShippingPackageOptionsInterface $itemOptions): bool
    {
        /** @var DpdShippingPackageOptionsInterface $mergedPackageProject */
        $mergedPackageProject = $this->addItemToPackage($this->currentPackage, $itemOptions);

        return $this->itemCanFit($mergedPackageProject);
    }

    /**
     * Tapes the current package if this is not the one too many
     *
     * @return bool
     */
    private function packCurrentPackage(): bool
    {
        if (count($this->packages) < $this->getParcelMaxAmount()) {
            $this->packages[] = $this->currentPackage;

            return true;
        }

        return false;
    }

    /**
     * Resets the current package
     *
     * @return void
     */
    private function resetCurrentPackage(): void
    {
        $this->currentPackage = $this->createPackageOptions(
            0,
            Dimensions::create(0, 0, 0),
            Price::create(0, self::CURRENCY)
        );
    }

    /**
     * Adds a package fragment to an existing package
     *
     * @param DpdShippingPackageOptionsInterface $basePackage
     * @param DpdShippingPackageOptionsInterface $addedPackage
     *
     * @return DpdShippingPackageOptionsInterface
     */
    private function addItemToPackage(
        DpdShippingPackageOptionsInterface $basePackage,
        DpdShippingPackageOptionsInterface $addedPackage
    ): DpdShippingPackageOptionsInterface {
        /** @var float $weight */
        $weight = $basePackage->getWeight() + $addedPackage->getWeight();
        /** @var Dimensions $dimensions */
        $dimensions = $this->getMergedPackageDimensions($basePackage, $addedPackage);

        /** @var Price $price */
        $cumulatedPrice = Price::create(
            $basePackage->getPrice()->getValue() + $addedPackage->getPrice()->getValue(),
            $basePackage->getPrice()->getCurrency()
        );

        return $this->createPackageOptions($weight, $dimensions, $cumulatedPrice);
    }

    /**
     * Turns weight and dimensions into a ShippingPackageOptions
     *
     * @param float      $weight
     * @param Dimensions $dimensions
     * @param Price      $price
     *
     * @return DpdShippingPackageOptionsInterface
     */
    private function createPackageOptions(
        float $weight,
        Dimensions $dimensions,
        Price $price
    ): DpdShippingPackageOptionsInterface {
        return $this->packageOptionsFactory->create(
            $dimensions,
            Weight::create(
                $weight,
                (new WeightUnit())->setCode(self::WEIGHT_UNIT)
            ),
            $price
        );
    }

    /**
     * Gets the dimensions of an assembly of two packages
     *
     * @param ShippingPackageOptionsInterface $basePackage
     * @param ShippingPackageOptionsInterface $itemOptions
     *
     * @return Dimensions
     */
    private function getMergedPackageDimensions(
        ShippingPackageOptionsInterface $basePackage,
        ShippingPackageOptionsInterface $itemOptions
    ): Dimensions {
        /** @var float $length */
        $length = max([$basePackage->getLength(), $itemOptions->getLength()]);
        /** @var float $width */
        $width = max([$basePackage->getWidth(), $itemOptions->getWidth()]);
        /** @var float $height */
        $height = $basePackage->getHeight() + $itemOptions->getHeight();

        return Dimensions::create($length, $width, $height, (new LengthUnit())->setCode(self::LENGTH_UNIT));
    }

    /**
     * Gets the different packages needed to ship all them lineItems
     *
     * @return ShippingPackageOptionsInterface[]
     */
    public function getPackages(): array
    {
        if ($this->currentPackage->getWeight() > 0 && !$this->packCurrentPackage()) {
            return [];
        }

        return $this->packages;
    }
}
