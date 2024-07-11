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
use Oro\Bundle\PricingBundle\Provider\WebsiteCurrencyProvider;
use Oro\Bundle\ShippingBundle\Context\ShippingLineItemInterface;
use Oro\Bundle\ShippingBundle\Entity\LengthUnit;
use Oro\Bundle\ShippingBundle\Entity\WeightUnit;
use Oro\Bundle\ShippingBundle\Model\Dimensions;
use Oro\Bundle\ShippingBundle\Model\ShippingPackageOptionsInterface;
use Oro\Bundle\ShippingBundle\Model\Weight;
use Oro\Bundle\ShippingBundle\Provider\MeasureUnitConversion;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShippingPackagesBuilder
{
    /**
     * DPD France length unit used for limitations
     */
    public const LENGTH_UNIT = 'm';
    /**
     * DPD France weight unit used for limitations
     */
    public const WEIGHT_UNIT = 'kg';

    private ?DpdShippingPackageOptions $currentPackage = null;
    /**
     * @var DpdShippingPackageOptionsInterface[]
     */
    private array $packages = [];
    private ?string $currencyCode = null;
    private ?int $websiteId = null;
    private ShippingService $shippingService;

    public function __construct(
        private readonly DpdShippingPackageOptionsFactoryInterface $packageOptionsFactory,
        private readonly MeasureUnitConversion $measureUnitConversion,
        private readonly SettingsProvider $settingsProvider,
        private readonly WebsiteCurrencyProvider $websiteCurrencyProvider
    ) {
    }

    /**
     * Init the builder for each shipping service
     */
    public function init(ShippingService $shippingService, int $websiteId): void
    {
        $this->websiteId = $websiteId;
        $this->currencyCode = $this->websiteCurrencyProvider->getWebsiteDefaultCurrency($websiteId);
        $this->resetCurrentPackage();
        $this->packages = [];
        $this->shippingService = $shippingService;
    }

    /**
     * Adds the given lineItem into the virtual pile of boxes
     *
     * @throws PackageException
     */
    public function addLineItem(\Oro\Bundle\ShippingBundle\Context\ShippingLineItem $lineItem): bool
    {
        if (!$lineItem->getWeight()) {
            $this->badItemException($lineItem, 'Unknown weight.');
        }
        if (!$lineItem->getDimensions()) {
            $this->badItemException($lineItem, 'Unknown size.');
        }
        if ($lineItem->getPrice()->getCurrency() !== $this->currencyCode) {
            $this->badItemException(
                $lineItem,
                sprintf('Lineitem price not defined in website\'s default currency: %s.', $this->currencyCode)
            );
        }

        $weight = $this->measureUnitConversion->convert($lineItem->getWeight(), self::WEIGHT_UNIT);
        $dimensions = $this->measureUnitConversion->convert($lineItem->getDimensions(), self::LENGTH_UNIT);

        if ($weight === null) {
            $this->badItemException($lineItem, sprintf('Could not convert the weight into %s.', self::WEIGHT_UNIT));
        }
        if (!$lineItem->getDimensions()) {
            $this->badItemException($lineItem, sprintf('Could not convert the dimensions into %s.', self::LENGTH_UNIT));
        }

        $itemOptions = $this->packageOptionsFactory->create($dimensions, $weight, $lineItem->getPrice());

        if (!$this->itemCanFit($itemOptions)) {
            $this->badItemException($lineItem, 'Either too heavy, too big or too pricey.');
        }
        for ($i = 0; $i < $lineItem->getQuantity(); $i++) {
            if (!$this->itemCanFitInCurrentPackage($itemOptions)) {
                if (!$this->packCurrentPackage()) {
                    throw new PackageException(
                        sprintf(
                            'Too many packages, need more than %d packages while %d are allowed for %s. Advise splitting order.',
                            count($this->packages) + 1,
                            $this->getParcelMaxAmount(),
                            $this->shippingService->getLabel()
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
     */
    private function getParcelMaxAmount(): ?int
    {
        $maxAmount = $this->shippingService->getParcelMaxAmount();
        if ($maxAmount === null) {
            //Fallback on general value set at integration level
            $maxAmount = $this->settingsProvider->getSettings()->get('dpd_fr_max_qty');
        }

        return $maxAmount;
    }

    /**
     * Helper to throw a PackageException for a given lineItem
     *
     * @throws PackageException
     */
    private function badItemException(\Oro\Bundle\ShippingBundle\Context\ShippingLineItem $lineItem, ?string $message = ''): void
    {
        throw new PackageException(
            sprintf(
                'The item %s (%s) cannot be shipped with %s. %s',
                $lineItem->getProduct()->getName(),
                $lineItem->getProduct()->getSku(),
                $this->shippingService->getLabel(),
                $message
            )
        );
    }

    /**
     * Is the package light and small enough to be shipped with the specified Dpd France service ?
     */
    private function itemCanFit(DpdShippingPackageOptionsInterface $itemOptions): bool
    {
        return (
            $itemOptions->getLength() <= $this->shippingService->getParcelMaxLength() &&
            $itemOptions->getWidth() <= $this->shippingService->getParcelMaxLength() &&
            $itemOptions->getHeight() <= $this->shippingService->getParcelMaxLength() &&
            $itemOptions->getWeight() <= $this->shippingService->getParcelMaxWeight() &&
            $itemOptions->getGirth() <= $this->shippingService->getParcelMaxPerimeter() &&
            $itemOptions->getPrice()->getValue() <= $this->shippingService->getParcelMaxValue()
        );
    }

    /**
     * Can the item be squeezed into the current package ?
     */
    private function itemCanFitInCurrentPackage(DpdShippingPackageOptionsInterface $itemOptions): bool
    {
        $mergedPackageProject = $this->addItemToPackage($this->currentPackage, $itemOptions);

        return $this->itemCanFit($mergedPackageProject);
    }

    /**
     * Tapes the current package if this is not the one too many
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
     */
    private function resetCurrentPackage(): void
    {
        $this->currentPackage = $this->createPackageOptions(
            0,
            Dimensions::create(0, 0, 0),
            Price::create(0, $this->currencyCode)
        );
    }

    /**
     * Adds a package fragment to an existing package
     */
    private function addItemToPackage(
        DpdShippingPackageOptionsInterface $basePackage,
        DpdShippingPackageOptionsInterface $addedPackage
    ): DpdShippingPackageOptionsInterface {
        $weight = $basePackage->getWeight() + $addedPackage->getWeight();
        $dimensions = $this->getMergedPackageDimensions($basePackage, $addedPackage);
        $cumulatedPrice = Price::create(
            $basePackage->getPrice()->getValue() + $addedPackage->getPrice()->getValue(),
            $basePackage->getPrice()->getCurrency()
        );

        return $this->createPackageOptions($weight, $dimensions, $cumulatedPrice);
    }

    /**
     * Turns weight and dimensions into a ShippingPackageOptions
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
     */
    private function getMergedPackageDimensions(
        ShippingPackageOptionsInterface $basePackage,
        ShippingPackageOptionsInterface $itemOptions
    ): Dimensions {
        $length = max([$basePackage->getLength(), $itemOptions->getLength()]);
        $width = max([$basePackage->getWidth(), $itemOptions->getWidth()]);
        $height = $basePackage->getHeight() + $itemOptions->getHeight();

        return Dimensions::create($length, $width, $height, (new LengthUnit())->setCode(self::LENGTH_UNIT));
    }

    /**
     * Gets the different packages needed to ship all them lineItems
     *
     * @return ShippingPackageOptionsInterface[]
     * @throws PackageException
     */
    public function getPackages(): array
    {
        if ($this->currentPackage->getWeight() > 0 && !$this->packCurrentPackage()) {
            throw new PackageException(
                sprintf(
                    'Too many packages, need more than %d packages while %d are allowed for %s. Advise splitting order.',
                    count($this->packages) + 1,
                    $this->getParcelMaxAmount(),
                    $this->shippingService->getLabel()
                )
            );
        }

        return $this->packages;
    }
}
