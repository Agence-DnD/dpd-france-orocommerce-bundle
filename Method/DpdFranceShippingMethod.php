<?php

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
     * additionnal cost method type option key
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
     * First number is Expedition ref
     * Second number is agency code
     * Third number is contract number
     *
     * @var string TRACKING_URL_PATTERN
     */
    public const TRACKING_URL_PATTERN = 'http://www.dpd.fr/tracer_%d_%d%d';
    /**
     * Description $icon field
     *
     * @var mixed $icon
     */
    protected $icon;
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
     * @param                            $identifier
     * @param                            $label
     * @param                            $icon
     * @param array                      $types
     * @param DpdFranceTransportSettings $transportSettings
     * @param bool                       $isEnabled
     */
    public function __construct(
        $identifier,
        $label,
        $icon,
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
    public function isGrouped()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsConfigurationFormType()
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
    public function getIcon()
    {
        // @TODO fetch icon from service ??
        $services = $this->transportSettings->getShippingServices();

        return $this->icon;
    }

    /**
     * {@inheritDoc}
     */
    public function getType($identifier)
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
    public function getTrackingLink($number)
    {
        // @TODO Implement getTrackingLink() method.
        // put the three digits in $nulmber, explode them and sprintf into the self::TRACKING_URL_PATTERN
        return $number;
    }

    /**
     * {@inheritDoc}
     */
    public function calculatePrices(ShippingContextInterface $context, array $methodOptions, array $optionsByTypes)
    {
        $methodSurcharge = $this->getSurchargeFromOptions($methodOptions);
        $result          = [];
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
     * @param array $option
     *
     * @return float
     */
    private function getSurchargeFromOptions(array $option): float
    {
        return (float)$option[static::OPTION_SURCHARGE];
    }
}
