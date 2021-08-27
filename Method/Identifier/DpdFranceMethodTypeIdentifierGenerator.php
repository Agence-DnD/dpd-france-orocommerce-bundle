<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Method\Identifier;

use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;

/**
 * Class DpdFranceMethodTypeIdentifierGenerator
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Method\Identifier
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceMethodTypeIdentifierGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generateIdentifier(Channel $channel, ShippingService $service)
    {
        return $service->getCode();
    }
}
