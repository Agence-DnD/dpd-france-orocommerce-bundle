<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle;

use Dnd\Bundle\DpdFranceShippingBundle\DependencyInjection\DndDpdFranceShippingExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The bundle for DPD France shipping method integration
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DndDpdFranceShippingBundle extends Bundle
{
    /**
     * {@inheritdoc}
     *
     * @return DndDpdFranceShippingExtension
     */
    public function getContainerExtension(): DndDpdFranceShippingExtension
    {
        if (null === $this->extension) {
            $this->extension = new DndDpdFranceShippingExtension();
        }

        return $this->extension;
    }
}
