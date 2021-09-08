<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\EntityExtendBundle\Provider\EnumValueProvider;
use Symfony\Component\Form\DataTransformerInterface;
use Extend\Entity\EV_Order_Internal_Status;

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
     * @return ArrayCollection
     */
    public function transform($value): ArrayCollection
    {
        /** @var string[] $ids */
        $ids = explode(',', $value ?? '');
        /** @var mixed[] $availableStatuses */
        $availableStatuses = $this->enumValueProvider->getEnumChoicesByCode('order_internal_status');
        /** @var ArrayCollection $items */
        $items = new ArrayCollection();
        /**
         * @var string $name
         * @var string $id
         */
        foreach ($availableStatuses as $name => $id) {
            if (!in_array($id, $ids, true)) {
                continue;
            }
            $items->add(new EV_Order_Internal_Status($id, $name));
        }
        return $items;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed[] $values
     *
     * @return string
     */
    public function reverseTransform($values): string
    {
        /** @var string[] $ids */
        $ids = [];
        /** @var AbstractEnumValue $value */
        foreach ($values as $value) {
            $ids[] = $value->getId();
        }

        return implode(',', $ids ?? []);
    }
}
