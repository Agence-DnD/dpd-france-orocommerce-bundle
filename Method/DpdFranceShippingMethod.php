<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Form\Type\DpdFranceShippingMethodOptionsType;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Context\ShippingContextInterface;
use Oro\Bundle\ShippingBundle\Method\PricesAwareShippingMethodInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodIconAwareInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodTypeInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingTrackingAwareInterface;

/**
 * Class DpdFranceShippingMethod
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Method
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethod implements ShippingMethodInterface, ShippingMethodIconAwareInterface, ShippingTrackingAwareInterface, PricesAwareShippingMethodInterface
{
    /**
     * Defines the order in which shipping methods appear on the user interface
     *
     * @var int SORT_ORDER
     */
    public const SORT_ORDER = 30;
    /**
     * Additional cost method type option key
     *
     * @var string OPTION_SURCHARGE
     */
    public const OPTION_SURCHARGE = 'surcharge';
    /**
     * The label for the method
     *
     * @var string LABEL
     */
    public const LABEL = 'DPD France';
    /**
     * The pattern for tracking URL.
     * First placeholder is protocol
     * Second placeholder is Expedition ref
     * Third placeholder is agency code
     * Fourth placeholder is contract number
     *
     * @var string TRACKING_URL_PATTERN
     */
    public const TRACKING_URL_PATTERN = '%s://www.dpd.fr/tracer_%s_%d%d';
    /**
     * Description $icon field
     *
     * @var string $icon
     */
    protected string $icon;
    /**
     * Description $transportSettings field
     *
     * @var DpdFranceTransportSettings $transportSettings
     */
    protected DpdFranceTransportSettings $transportSettings;
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
     * Description $isEnabled field
     *
     * @var bool $isEnabled
     */
    private bool $isEnabled;
    /**
     * @var ShippingMethodTypeInterface[] $types
     */
    private array $types;

    /**
     * DpdFranceShippingMethod constructor
     *
     * @param string                     $identifier
     * @param string                     $label
     * @param string                     $icon
     * @param array                      $types
     * @param DpdFranceTransportSettings $transportSettings
     * @param bool                       $isEnabled
     */
    public function __construct(
        string $identifier,
        string $label,
        string $icon,
        array $types,
        DpdFranceTransportSettings $transportSettings,
        bool $isEnabled
    ) {
        $this->identifier        = $identifier;
        $this->label             = $label;
        $this->isEnabled         = $isEnabled;
        $this->transportSettings = $transportSettings;
        $this->types             = $types;
        $this->icon              = $icon;
    }

    /**
     * Description getIdentifier function
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Description getLabel function
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Description isEnabled function
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Description getTypes function
     *
     * @return ShippingMethodTypeInterface[]
     */
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

    /**
     * Description getSortOrder function
     *
     * @return int
     */
    public function getSortOrder(): int
    {
        return static::SORT_ORDER;
    }

    /**
     * {@inheritDoc}
     */
    public function getIcon(): string
    {
        /** @var ShippingService $service */
        $service = $this->transportSettings->getShippingService($this->getIdentifier());

        return $service !== null ? $service->getIcon() : '';
    }

    /**
     * {@inheritDoc}
     */
    public function getType($identifier): ?ShippingMethodTypeInterface
    {
        /** @var ShippingMethodTypeInterface[] $methodTypes */
        $methodTypes = $this->getTypes();
        if ($methodTypes !== null) {
            /** @var ShippingMethodTypeInterface $methodType */
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
        /** @var string[] $numbers */
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
        /** @var float $methodSurcharge */
        $methodSurcharge = $this->getSurchargeFromOptions($methodOptions);
        /** @var Price[] $result */
        $result = [];
        /**
         * @var string  $typeId
         * @var mixed[] $option
         */
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
     *
     * @param mixed[] $option
     *
     * @return float
     */
    private function getSurchargeFromOptions(array $option): float
    {
        return (float)($option[static::OPTION_SURCHARGE] ?? 0);
    }
}
