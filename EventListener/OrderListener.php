<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\EventListener;

use Dnd\Bundle\DpdFranceShippingBundle\Provider\StationExportProvider;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Component\MessageQueue\Transport\Exception\Exception;

/**
 * Class OrderListener
 *
 * @package   Dnd\Commerce\Bundle\OrderBundle\EventListener\Flux
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class OrderListener
{
    /**
     * Description $stationExportProvider field
     *
     * @var StationExportProvider $stationExportProvider
     */
    protected StationExportProvider $stationExportProvider;

    /**
     * OrderListener constructor
     *
     * @param StationExportProvider $stationExportProvider
     */
    public function __construct(
        StationExportProvider $stationExportProvider
    ) {
        $this->stationExportProvider = $stationExportProvider;
    }

    /**
     * Description postUpdate function
     *
     * @param Order $order
     *
     * @return void
     * @throws Exception
     */
    public function postUpdate(Order $order): void
    {
        if ($this->stationExportProvider->isStationExportEnabled()) {
            $this->stationExportProvider->queueIfExportable($order);
        }
    }
}
