<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method\Identifier;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Oro\Bundle\IntegrationBundle\Entity\Channel;

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
     * Description generateIdentifier function
     *
     * @param Channel         $channel
     * @param ShippingService $service
     *
     * @return string
     */
    public function generateIdentifier(Channel $channel, ShippingService $service): string
    {
        return $service->getCode();
    }
}
