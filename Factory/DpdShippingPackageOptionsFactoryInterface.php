<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Factory;

use Dnd\Bundle\DpdFranceShippingBundle\Model\DpdShippingPackageOptionsInterface;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Model\Dimensions;
use Oro\Bundle\ShippingBundle\Model\Weight;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
interface DpdShippingPackageOptionsFactoryInterface
{
    public function create(Dimensions $dimensions, Weight $weight, Price $price): DpdShippingPackageOptionsInterface;
}
