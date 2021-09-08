<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Factory;

use Dnd\Bundle\DpdFranceShippingBundle\Model\DpdShippingPackageOptions;
use Dnd\Bundle\DpdFranceShippingBundle\Model\DpdShippingPackageOptionsInterface;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Model\Dimensions;
use Oro\Bundle\ShippingBundle\Model\Weight;

class DpdShippingPackageOptionsFactory implements DpdShippingPackageOptionsFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(Dimensions $dimensions, Weight $weight, Price $price): DpdShippingPackageOptionsInterface
    {
        return new DpdShippingPackageOptions($dimensions, $weight, $price);
    }
}
