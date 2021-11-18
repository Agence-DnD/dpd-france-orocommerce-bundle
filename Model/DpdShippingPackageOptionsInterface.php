<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Model;

use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Model\ShippingPackageOptionsInterface;

/**
 * Interface DpdShippingPackageOptionsInterface
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Model
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
interface DpdShippingPackageOptionsInterface extends ShippingPackageOptionsInterface
{
    /**
     * The cumulated value of the package
     *
     * @return Price
     */
    public function getPrice(): Price;
}
