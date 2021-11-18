<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Form\DataTransformer;

use Extend\Entity\EV_Order_Internal_Status;
use Oro\Bundle\EntityExtendBundle\Provider\EnumValueProvider;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class OrderStatusTransformer
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Form\DataTransformer
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class OrderStatusTransformer implements DataTransformerInterface
{
    /**
     * Description $enumValueProvider field
     *
     * @var EnumValueProvider $enumValueProvider
     */
    protected EnumValueProvider $enumValueProvider;

    /**
     * OrderStatusTransformer constructor
     *
     * @param EnumValueProvider $enumValueProvider
     */
    public function __construct(
        EnumValueProvider $enumValueProvider
    ) {
        $this->enumValueProvider = $enumValueProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @param string|null $value
     *
     * @return EV_Order_Internal_Status|null
     */
    public function transform($value): ?EV_Order_Internal_Status
    {
        /** @var mixed[] $availableStatuses */
        $availableStatuses = $this->enumValueProvider->getEnumChoicesByCode('order_internal_status');
        /**
         * @var string $name
         * @var string $id
         */
        foreach ($availableStatuses as $name => $id) {
            if ($value === $id) {
                return new EV_Order_Internal_Status($id, $name);
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @param EV_Order_Internal_Status|null $value
     *
     * @return string|null
     */
    public function reverseTransform($value): ?string
    {
        return $value !== null ? $value->getId() : null;
    }
}
