<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Form\Type;

use Oro\Bundle\EntityConfigBundle\Entity\FieldConfigModel;
use Oro\Bundle\EntityConfigBundle\Manager\AttributeManager;
use Oro\Bundle\FormBundle\Form\Type\Select2EntityType;
use Oro\Bundle\ProductBundle\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeMappingType extends AbstractType
{
    /**
     * Description CLASS_NAME constant
     *
     * @var string CLASS_NAME
     */
    public const CLASS_NAME = FieldConfigModel::class;
    /**
     * Description NAME constant
     *
     * @var string NAME
     */
    public const NAME = 'product_attribute_select_type';
    /**
     * @var AttributeManager
     */
    protected $attributeManager;

    /**
     * @param AttributeManager $attributeManager
     */
    public function __construct(AttributeManager $attributeManager)
    {
        $this->attributeManager = $attributeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return Select2EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'label'                => 'oro.entity_config.attribute.entity_label',
                'class'                => self::CLASS_NAME,
                'choice_label'         => 'field_name',
                'random_id'            => true,
                'choices'              => $this->attributeManager->getAttributesByClass(Product::class),
                'configs'              => [
                    'allowClear'  => true,
                    'placeholder' => 'dnd_dpd_france_shipping.form.select_attribute.label',
                ],
                'entities'             => [],
                'translatable_options' => false,
            ]);
    }
}
