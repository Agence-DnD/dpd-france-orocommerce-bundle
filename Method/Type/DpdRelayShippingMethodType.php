<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Method\Type;

use Dnd\Bundle\DpdFranceShippingBundle\Form\Type\DpdFranceShippingMethodOptionsType;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Context\ShippingContextInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingTrackingAwareInterface;

/**
 * Class DpdRelayShippingMethodType
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Method\Type
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdRelayShippingMethodType extends AbstractDpdFranceShippingMethodType implements ShippingTrackingAwareInterface
{
    /**
     * Description IDENTIFIER constant
     *
     * @var string IDENTIFIER
     */
    public const IDENTIFIER = 'dpd_fr_relay';
    /**
     * {@inheritDoc}
     */
    public function getSortOrder(): int
    {
        return 50;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsConfigurationFormType(): string
    {
        return DpdFranceShippingMethodOptionsType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function calculatePrice(ShippingContextInterface $context, array $methodOptions, array $typeOptions): ?Price
    {
        $price = $typeOptions[static::PRICE_OPTION];

        dump(__CLASS__ . '::' . __METHOD__);

        // Provide additional price calculation logic here if required. @TODO

        return Price::create((float)$price, $context->getCurrency());
    }

    /**
     * {@inheritDoc}
     */
    public function getTrackingLink($number)
    {
        dump(__CLASS__ . '::' . __METHOD__);
        dump($number);
        // @TODO Implement getTrackingLink() method.
    }
}
