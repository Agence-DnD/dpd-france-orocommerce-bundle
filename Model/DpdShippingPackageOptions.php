<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Model;

use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Model\Dimensions;
use Oro\Bundle\ShippingBundle\Model\ShippingPackageOptions;
use Oro\Bundle\ShippingBundle\Model\Weight;

/**
 * Class DpdShippingPackageOptions
 *
 * @package   DpdShippingPackageOptions
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdShippingPackageOptions extends ShippingPackageOptions implements DpdShippingPackageOptionsInterface
{
    /**
     * Description $price field
     *
     * @var Price $price
     */
    private Price $price;

    public function __construct(
        Dimensions $dimensions,
        Weight $weight,
        Price $price
    ) {
        parent::__construct($dimensions, $weight);

        $this->price = $price;
    }

    /**
     * Description getPrice function
     *
     * @return Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * Description setPrice function
     *
     * @param Price $price
     *
     * @return void
     */
    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }
}
