<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Form\Type\DpdFranceShippingMethodOptionsType;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Context\ShippingContextInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodTypeInterface;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethodType implements ShippingMethodTypeInterface
{
    /**
     * Price option index
     */
    public const PRICE_OPTION = 'price';

    protected string $methodId;
    private string $identifier;
    private string $label;

    public function __construct(
        string $identifier,
        string $label,
        string $methodId,
        private readonly DpdFranceTransportSettings $settings,
        private readonly ShippingService $shippingService
    ) {
        $this->identifier = $identifier;
        $this->label = $label;
        $this->methodId = $methodId;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function isGrouped(): bool
    {
        return false;
    }

    public function getLabel(): string
    {
        return $this->label;
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
    public function getSortOrder(): int
    {
        return 150;
    }

    /**
     * {@inheritDoc}
     */
    public function calculatePrice(ShippingContextInterface $context, array $methodOptions, array $typeOptions): ?Price
    {
        if (!$context->getShippingAddress()) {
            return null;
        }

        $methodSurcharge = $this->getSurchargeFromOptions($methodOptions);
        $typeSurcharge = $this->getSurchargeFromOptions($typeOptions);

        return Price::create($methodSurcharge + $typeSurcharge, $context->getCurrency());
    }

    private function getSurchargeFromOptions(array $option): float
    {
        return (float)($option[DpdFranceShippingMethod::OPTION_SURCHARGE] ?? 0);
    }
}
