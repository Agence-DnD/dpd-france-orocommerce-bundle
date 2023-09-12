<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Integration;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
interface DpdFranceTransportInterface extends TransportInterface
{
    public function getTransportEntity(): DpdFranceTransportSettings;

    public function getSettings(): ParameterBag;
}
