<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Model;

use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Model\ShippingPackageOptions;
use Oro\Bundle\ShippingBundle\Model\Weight;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdShippingPackageOptions extends ShippingPackageOptions implements DpdShippingPackageOptionsInterface
{
    public function __construct(
        protected Weight $weight,
        protected Price $price
    ) {
        parent::__construct($dimensions, $weight);

        $this->price = $price;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }
}
