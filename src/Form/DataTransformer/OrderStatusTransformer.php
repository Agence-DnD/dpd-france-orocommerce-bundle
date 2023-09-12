<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Form\DataTransformer;

use Extend\Entity\EV_Order_Internal_Status;
use Oro\Bundle\EntityExtendBundle\Provider\EnumValueProvider;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class OrderStatusTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly EnumValueProvider $enumValueProvider
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function transform(mixed $value): ?EV_Order_Internal_Status
    {
        $availableStatuses = $this->enumValueProvider->getEnumChoicesByCode('order_internal_status');
        foreach ($availableStatuses as $name => $id) {
            if ($value === $id) {
                return new EV_Order_Internal_Status($id, $name);
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform(mixed $value): ?string
    {
        return $value !== null ? $value->getId() : null;
    }
}
