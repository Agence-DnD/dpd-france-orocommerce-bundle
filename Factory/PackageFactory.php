<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Factory;

use Dnd\Bundle\DpdFranceShippingBundle\Builder\ShippingPackagesBuilder;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
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
     * @todo add an exception mechanism and log them somewhere in the order to give some insights to customer service
     *
     */
    public function create(
        ShippingLineItemCollectionInterface $lineItemCollection,
        ShippingService $shippingService
    ): array {
        $this->packagesBuilder->init($shippingService);
        /** @var ShippingLineItemInterface $item */
        foreach ($lineItemCollection as $item) {
            if (!$item->getWeight() || !$item->getDimensions()) {
                // Can't take the risk of shipping the order with a product with unknown dimensions or weight
                return [];
            }

            if (!$this->packagesBuilder->addLineItem($item)) {
                // This product is either too big/heavy or requires more boxes than DPD can accept
                return [];
            }
        }

        return $this->packagesBuilder->getPackages();
    }
}
