<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\EventListener;

use Dnd\Bundle\DpdFranceShippingBundle\Provider\StationExportProvider;
use Oro\Bundle\OrderBundle\Entity\Order;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class OrderListener
{
    public function __construct(
        private readonly StationExportProvider $stationExportProvider
    ) {
    }

    public function postUpdate(Order $order): void
    {
        if ($this->stationExportProvider->isStationExportEnabled()) {
            $this->stationExportProvider->queueIfExportable($order);
        }
    }
}
