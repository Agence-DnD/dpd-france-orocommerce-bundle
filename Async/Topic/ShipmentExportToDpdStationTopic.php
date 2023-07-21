<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Async\Topic;

use Oro\Component\MessageQueue\Topic\AbstractTopic;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShipmentExportToDpdStationTopic extends AbstractTopic
{
    public static function getName(): string
    {
        return 'dnd_dpd_france_shipping.flow.station_export';
    }

    public static function getDescription(): string
    {
        return 'Export to DPD Station';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(['orderId'])
            ->setRequired(['orderId'])
        ;
    }
}
