<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Builder;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\PackageException;
use Oro\Bundle\ShippingBundle\Context\ShippingLineItemInterface;
use Oro\Bundle\ShippingBundle\Entity\LengthUnit;
use Oro\Bundle\ShippingBundle\Entity\WeightUnit;
use Oro\Bundle\ShippingBundle\Factory\ShippingPackageOptionsFactoryInterface;
use Oro\Bundle\ShippingBundle\Model\Dimensions;
use Oro\Bundle\ShippingBundle\Model\ShippingPackageOptions;
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
     * Description $measureUnitConversion field
     *
     * @var MeasureUnitConversion $measureUnitConversion
     */
    protected MeasureUnitConversion $measureUnitConversion;
    /**
     * @var ShippingPackageOptionsFactoryInterface
     */
    private ShippingPackageOptionsFactoryInterface $packageOptionsFactory;
    /**
     * Description $currentPackage field
     *
     * @var ShippingPackageOptions|null $currentPackage
     */
    private ?ShippingPackageOptions $currentPackage = null;
    /**
     * @var ShippingPackageOptionsInterface[]
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
     * @param ShippingPackageOptionsFactoryInterface $packageOptionsFactory
     */
    public function __construct(
        ShippingPackageOptionsFactoryInterface $packageOptionsFactory,
        MeasureUnitConversion $measureUnitConversion
    ) {
        $this->packageOptionsFactory = $packageOptionsFactory;
        $this->measureUnitConversion = $measureUnitConversion;
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
        $weight     = $this->measureUnitConversion->convert($lineItem->getWeight(), self::WEIGHT_UNIT);
        $dimensions = $this->measureUnitConversion->convert($lineItem->getDimensions(), self::LENGTH_UNIT);

        $itemOptions = $this->packageOptionsFactory->create($dimensions, $weight);

        if (!$this->itemCanFit($itemOptions)) {
            throw new PackageException(
                sprintf(
                    'The item %s (%s) cannot be shipped with DPD FR. Either too heavy or too big',
                    $lineItem->getProduct()->getName(),
                    $lineItem->getProduct()->getSku()
                )
            );
        }
        for ($i = 0; $i < $lineItem->getQuantity(); $i++) {
            if (!$this->itemCanFitInCurrentPackage($itemOptions)) {
                if (!$this->packCurrentPackage()) {
                    throw new PackageException(
                        sprintf(
                            'Too many packages, need more than %d packages while %d are allowed. Advise splitting order.',
                            count($this->packages) + 1,
                            $this->shippingService->getParcelMaxAmount()
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
     * Is the package light and small enough to be shipped with the specified Dpd France service ?
     *
     * @param ShippingPackageOptionsInterface $itemOptions
     *
     * @return bool
     */
    private function itemCanFit(ShippingPackageOptionsInterface $itemOptions): bool
    {
        if ($itemOptions->getLength() > $this->shippingService->getParcelMaxLength()) {
            return false;
        }
        if ($itemOptions->getWidth() > $this->shippingService->getParcelMaxLength()) {
            return false;
        }

        if ($itemOptions->getHeight() > $this->shippingService->getParcelMaxLength()) {
            return false;
        }

        if ($itemOptions->getWeight() > $this->shippingService->getParcelMaxWeight()) {
            return false;
        }

        return ($itemOptions->getGirth() < $this->shippingService->getParcelMaxPerimeter());
    }

    /**
     * Can the item be squeezed into the current package ?
     *
     * @param ShippingPackageOptionsInterface $itemOptions
     *
     * @return bool
     */
    private function itemCanFitInCurrentPackage(ShippingPackageOptionsInterface $itemOptions): bool
    {
        /** @var ShippingPackageOptionsInterface $mergedPackageProject */
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
        if (count($this->packages) < $this->shippingService->getParcelMaxAmount()) {
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
        $this->currentPackage = $this->createPackageOptions(0, Dimensions::create(0, 0, 0, null));
    }

    /**
     * Adds a package fragment to an existing package
     *
     * @param ShippingPackageOptionsInterface $basePackage
     * @param ShippingPackageOptionsInterface $addedPackage
     *
     * @return ShippingPackageOptionsInterface
     */
    private function addItemToPackage(
        ShippingPackageOptionsInterface $basePackage,
        ShippingPackageOptionsInterface $addedPackage
    ): ShippingPackageOptionsInterface {
        $weight = $basePackage->getWeight() + $addedPackage->getWeight();

        $dimensions = $this->getMergedPackageDimensions($basePackage, $addedPackage);

        return $this->createPackageOptions($weight, $dimensions);
    }

    /**
     * Turns weight and dimensions into a ShippingPackageOptions
     *
     * @param float      $weight
     * @param Dimensions $dimensions
     *
     * @return ShippingPackageOptionsInterface
     */
    private function createPackageOptions(float $weight, Dimensions $dimensions): ShippingPackageOptionsInterface
    {
        return $this->packageOptionsFactory->create(
            $dimensions,
            Weight::create(
                $weight,
                (new WeightUnit())->setCode(self::WEIGHT_UNIT)
            )
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
        $length = max([$basePackage->getLength(), $itemOptions->getLength()]);
        $width  = max([$basePackage->getWidth(), $itemOptions->getWidth()]);
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
