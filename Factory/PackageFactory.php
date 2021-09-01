<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Factory;

use Dnd\Bundle\DpdFranceShippingBundle\Builder\ShippingPackagesBuilder;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\PackageException;
use Oro\Bundle\ShippingBundle\Context\LineItem\Collection\ShippingLineItemCollectionInterface;
use Oro\Bundle\ShippingBundle\Context\ShippingLineItemInterface;
use Oro\Bundle\ShippingBundle\Model\ShippingPackageOptionsInterface;

/**
 * Class PackageFactory
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Factory
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class PackageFactory
{
    /**
     * The packages builder
     *
     * @var ShippingPackagesBuilder $packagesBuilder
     */
    protected ShippingPackagesBuilder $packagesBuilder;

    /**
     * PackageFactory constructor
     */
    public function __construct(ShippingPackagesBuilder $packagesBuilder)
    {
        $this->packagesBuilder = $packagesBuilder;
    }

    /**
     * Creates a virtual pile of boxes to be shipped with DPD France
     *
     * @param ShippingLineItemCollectionInterface $lineItemCollection
     * @param ShippingService                     $shippingService
     *
     * @return ShippingPackageOptionsInterface[]
     * @throws PackageException
     */
    public function create(
        ShippingLineItemCollectionInterface $lineItemCollection,
        ShippingService $shippingService
    ): array {
        $this->packagesBuilder->init($shippingService);
        /** @var ShippingLineItemInterface $item */
        foreach ($lineItemCollection as $item) {
            if (!$item->getWeight() || !$item->getDimensions()) {
                throw new PackageException(
                    sprintf(
                        'The item %s (%s) cannot be shipped with DPD FR. Unknown size and/or weight.',
                        $item->getProduct()->getName(),
                        $item->getProduct()->getSku()
                    )
                );
            }
            $this->packagesBuilder->addLineItem($item);
        }

        return $this->packagesBuilder->getPackages();
    }
}
