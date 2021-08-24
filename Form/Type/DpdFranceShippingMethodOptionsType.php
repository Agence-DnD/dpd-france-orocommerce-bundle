<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Form\Type;

use Oro\Bundle\FlatRateShippingBundle\Form\Type\FlatRateOptionsType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DpdFranceShippingMethodOptionsType
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Form\Type
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethodOptionsType extends FlatRateOptionsType
{
    /**
     * Description BLOCK_PREFIX constant
     *
     * @var string BLOCK_PREFIX
     */
    public const BLOCK_PREFIX = 'dpd_france_shipping_options_type';

    /**
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'dnd_dpd_france_shipping.form.options_type.label',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
