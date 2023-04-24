<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Form\Type\DpdFranceShippingMethodOptionsType;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Context\ShippingContextInterface;
use Oro\Bundle\ShippingBundle\Method\PricesAwareShippingMethodInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodIconAwareInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodTypeInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingTrackingAwareInterface;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethod implements ShippingMethodInterface, ShippingMethodIconAwareInterface, ShippingTrackingAwareInterface, PricesAwareShippingMethodInterface
{
    /**
     * Defines the order in which shipping methods appear on the user interface
     */
    public const SORT_ORDER = 30;
    /**
     * Additional cost method type option key
     */
    public const OPTION_SURCHARGE = 'surcharge';
    /**
     * The label for the method
     */
    public const LABEL = 'DPD France';
    /**
     * The pattern for tracking URL.
     * First placeholder is protocol
     * Second placeholder is Expedition ref
     * Third placeholder is agency code
     * Fourth placeholder is contract number
     */
    public const TRACKING_URL_PATTERN = '%s://www.dpd.fr/tracer_%s_%d%d';

    protected string $icon;
    private string $identifier;
    private string $label;
    private bool $isEnabled;
    private array $types;

    public function __construct(
        string $identifier,
        string $label,
        string $icon,
        array $types,
        private readonly DpdFranceTransportSettings $transportSettings,
        bool $isEnabled
    ) {
        $this->identifier = $identifier;
        $this->label = $label;
        $this->isEnabled = $isEnabled;
        $this->types = $types;
        $this->icon = $icon;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * {@inheritDoc}
     */
    public function isGrouped(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsConfigurationFormType(): string
    {
        return DpdFranceShippingMethodOptionsType::class;
    }

    public function getSortOrder(): int
    {
        return static::SORT_ORDER;
    }

    /**
     * {@inheritDoc}
     */
    public function getIcon(): string
    {
        $service = $this->transportSettings->getShippingService($this->getIdentifier());

        return $service !== null ? $service->getIcon() : '';
    }

    /**
     * {@inheritDoc}
     */
    public function getType($identifier): ?ShippingMethodTypeInterface
    {
        $methodTypes = $this->getTypes();
        if ($methodTypes !== null) {
            foreach ($methodTypes as $methodType) {
                if ($methodType->getIdentifier() === (string)$identifier) {
                    return $methodType;
                }
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getTrackingLink($number): ?string
    {
        $numbers = explode('_', $number);
        if (count($numbers) === 3) {
            return sprintf(self::TRACKING_URL_PATTERN, 'https', $numbers[0], (int)$numbers[1], (int)$numbers[2]);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function calculatePrices(
        ShippingContextInterface $context,
        array $methodOptions,
        array $optionsByTypes
    ): array {
        $methodSurcharge = $this->getSurchargeFromOptions($methodOptions);
        $result = [];
        foreach ($optionsByTypes as $typeId => $option) {
            $result[$typeId] = Price::create(
                $methodSurcharge + $this->getSurchargeFromOptions($option),
                $context->getCurrency()
            );
        }

        return $result;
    }

    /**
     * Gets the price surcharge for the given type option
     */
    private function getSurchargeFromOptions(array $option): float
    {
        return (float)($option[static::OPTION_SURCHARGE] ?? 0);
    }
}
