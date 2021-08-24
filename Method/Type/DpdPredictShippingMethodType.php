<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Method\Type;

use Dnd\Bundle\DpdFranceShippingBundle\Form\Type\DpdFranceShippingMethodOptionsType;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Context\ShippingContextInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingTrackingAwareInterface;

class DpdPredictShippingMethodType extends AbstractDpdFranceShippingMethodType implements ShippingTrackingAwareInterface
{
    /**
     * Description IDENTIFIER constant
     *
     * @var string IDENTIFIER
     */
    public const IDENTIFIER = 'dpd_fr_predict';
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

    public function getTrackingLink($number)
    {
        // @TODO Implement getTrackingLink() method.
    }
}
