<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Async;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class Topics
{
    /**
     * The MQ identifier used for the DPD FR STATION shipment export
     */
    public const SHIPMENT_EXPORT_TO_DPD_STATION = 'dnd_dpd_france_shipping.flow.station_export';
    /**
     * The MQ identifier used for the DPD FR STATION shipment forced export (when exported again)
     */
    public const SHIPMENT_EXPORT_TO_DPD_STATION_FORCED = 'dnd_dpd_france_shipping.flow.station_export_forced';
}
