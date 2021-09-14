<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Factory;

use Dnd\Bundle\DpdFranceShippingBundle\Model\DpdShippingPackageOptionsInterface;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Model\Dimensions;
use Oro\Bundle\ShippingBundle\Model\Weight;

interface DpdShippingPackageOptionsFactoryInterface
{
    /**
     * Description create function
     *
     * @param Dimensions $dimensions
     * @param Weight     $weight
     * @param Price      $price
     *
     * @return DpdShippingPackageOptionsInterface
     */
    public function create(Dimensions $dimensions, Weight $weight, Price $price): DpdShippingPackageOptionsInterface;
}
