<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Form\Type;

use Dnd\Bundle\DpdFranceShippingBundle\Method\DpdFranceShippingMethod;
use Oro\Bundle\FlatRateShippingBundle\Form\Type\FlatRateOptionsType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethodOptionsType extends FlatRateOptionsType
{
    /**
     * Block prefix
     */
    public const BLOCK_PREFIX = 'dpd_france_shipping_config_options';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(DpdFranceShippingMethod::OPTION_SURCHARGE, NumberType::class, [
            'required' => true,
            'label' => 'dnd_dpd_france_shipping.form.shipping_method_config_options.surcharge.label',
            'scale' => $this->roundingService->getPrecision(),
            'rounding_mode' => $this->roundingService->getRoundType(),
            'attr' => [
                'data-scale' => $this->roundingService->getPrecision(),
                'class' => 'method-options-additional-cost',
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return self::BLOCK_PREFIX;
    }
}
