<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method\Type;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Form\Type\DpdFranceShippingMethodOptionsType;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodTypeInterface;

/**
 * Class AbstractDpdFranceShippingMethod
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Method
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
abstract class AbstractDpdFranceShippingMethodType implements ShippingMethodTypeInterface
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
     * AbstractDpdFranceShippingMethodType constructor
     *
     * @param string                     $identifier
     * @param string                     $label
     * @param DpdFranceTransportSettings $settings
     */
    public function __construct(string $identifier, string $label, DpdFranceTransportSettings $settings)
    {
        $this->identifier = $identifier;
        $this->label      = $label;
        $this->settings = $settings;
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
}
