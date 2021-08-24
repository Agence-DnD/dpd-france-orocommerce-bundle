<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Method\Type;

use Dnd\Bundle\DpdFranceShippingBundle\Form\Type\DpdFranceShippingMethodOptionsType;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Context\ShippingContextInterface;

class DpdClassicShippingMethodType extends AbstractDpdFranceShippingMethodType
{
    /**
     * Description IDENTIFIER constant
     *
     * @var string IDENTIFIER
     */
    public const IDENTIFIER = 'dpd_fr_classic';

    /**
     * {@inheritDoc}
     */
    public function getSortOrder(): int
    {
        return 0;
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
}
