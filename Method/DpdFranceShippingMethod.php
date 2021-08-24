<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method;

use Oro\Bundle\FlatRateShippingBundle\Method\FlatRateMethodType;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodIconAwareInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class DpdFranceShippingMethod
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Method
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethod implements ShippingMethodInterface, ShippingMethodIconAwareInterface
{
    /**
     * Description $types field
     *
     * @var array $types
     */
    private array $types;
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
     * Description $icon field
     *
     * @var string|null $icon
     */
    private ?string $icon;
    /**
     * Description $type field
     *
     * @var FlatRateMethodType $type
     */
    private FlatRateMethodType $type;
    /**
     * Description $enabled field
     *
     * @var bool $enabled
     */
    private bool $enabled;

    /**
     * DpdFranceShippingMethod constructor
     *
     * @param string      $identifier
     * @param string      $label
     * @param string|null $icon
     * @param bool        $enabled
     * @param array       $types
     */
    public function __construct(string $identifier, string $label, ?string $icon, bool $enabled, array $types)
    {
        $this->identifier = $identifier;
        $this->label      = $label;
        $this->icon       = $icon;
        //$this->type       = new FlatRateMethodType($label);
        $this->enabled    = $enabled;
        $this->types      = $types;
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
    public function isEnabled(): bool
    {
        return $this->enabled;
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
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * {@inheritDoc}
     */
    public function getType($identifier): ?ShippingMethodTypeInterface
    {
        $methodTypes = $this->getTypes();
        if ($methodTypes !== null) {
            foreach ($methodTypes as $methodType) {
                if ($methodType->getIdentifier() === (string) $identifier) {
                    return $methodType;
                }
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsConfigurationFormType(): string
    {
        return HiddenType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getSortOrder(): int
    {
        return 150;
    }
}
