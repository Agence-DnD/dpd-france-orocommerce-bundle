<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method\Identifier;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Oro\Bundle\IntegrationBundle\Entity\Channel;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceMethodTypeIdentifierGenerator
{
    public function generateIdentifier(Channel $channel, ShippingService $service): string
    {
        return $service->getCode();
    }
}
