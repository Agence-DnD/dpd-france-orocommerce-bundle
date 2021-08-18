<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @return ArrayCollection
     */
    public function transform($value): ArrayCollection
    {
        //@TODO FIXME
        $ids = explode(',', $value ?? '');

        $availableStatuses = $this->enumValueProvider->getEnumChoicesByCode('order_internal_status');

        $statuses = array_filter($availableStatuses, static function($id) use (&$ids) {
            return in_array($id, $ids, true);
        });
        return new ArrayCollection($statuses);
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
        $ids = [];
        foreach ($values as $value) {
            $ids[] = $value->getId();
        }
        return implode(',', $ids ?? []);
    }

}
