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
 * Class DpdFranceShippingMethodType
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Method\Type
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethodType implements ShippingMethodTypeInterface
{
    /**
     * Description PRICE_OPTION constant
     *
     * @var string PRICE_OPTION
     */
    public const PRICE_OPTION = 'price';
    /**
     * Description $settings field
     *
     * @var DpdFranceTransportSettings $settings
     */
    protected DpdFranceTransportSettings $settings;
    /**
     * Description $shippingService field
     *
     * @var ShippingService $shippingService
     */
    protected ShippingService $shippingService;
    /**
     * Description $methodId field
     *
     * @var string $methodId
     */
    protected string $methodId;
    /**
     * Description $identifier field
     *
     * @var string $identifier
     */
    private string $identifier;
    /**
     * Description $label field
     *
     * @var string $label
     */
    private string $label;

    /**
     * DpdFranceShippingMethodType constructor
     *
     * @param string                     $identifier
     * @param string                     $label
     * @param DpdFranceTransportSettings $settings
     * @param ShippingService            $shippingService
     */
    public function __construct(
        string $identifier,
        string $label,
        string $methodId,
        DpdFranceTransportSettings $settings,
        ShippingService $shippingService
    ) {
        $this->identifier      = $identifier;
        $this->label           = $label;
        $this->methodId        = $methodId;
        $this->settings        = $settings;
        $this->shippingService = $shippingService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function isGrouped(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
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
    public function calculatePrice(ShippingContextInterface $context, array $methodOptions, array $typeOptions)
    {
        if (!$context->getShippingAddress()) {
            return null;
        }

        $methodSurcharge = $this->getSurchargeFromOptions($methodOptions);
        $typeSurcharge   = $this->getSurchargeFromOptions($typeOptions);

        return Price::create($methodSurcharge + $typeSurcharge, $context->getCurrency());
    }

    /**
     * @param array $option
     *
     * @return float
     */
    private function getSurchargeFromOptions(array $option): float
    {
        return (float)$option[DpdFranceShippingMethod::OPTION_SURCHARGE];
    }
}
