<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Provider;

use Dnd\Bundle\DpdFranceShippingBundle\Async\Topics;
use Dnd\Bundle\DpdFranceShippingBundle\Method\DpdFranceShippingMethodProvider;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Oro\Component\MessageQueue\Transport\Exception\Exception;
use Oro\Component\MessageQueue\Util\JSON;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class StationExportProvider
{
    public function __construct(
        private readonly MessageProducerInterface $producer,
        private readonly SettingsProvider $settingsProvider,
        private readonly TranslatorInterface $translator
    ) {
    }

    /**
     * Enqueues an order for DPD FR station export if eligible
     *
     * @throws Exception|\JsonException
     */
    public function queueIfExportable(Order $order, ?bool $forced = false): array
    {
        $result = [
            'successful' => false,
            'error' => [],
        ];
        try {
            if (!$this->isOrderExportable($order, $forced)) {
                $result['error'] = $this->translator->trans(
                    "dnd_dpd_france_shipping.action.message.order.station_export.error.requirements"
                );

                return $result;
            }
        } catch (\InvalidArgumentException $e) {
            $result['error'] = $this->translator->trans(
                'dnd_dpd_france_shipping.action.message.order.station_export.error.missing_integration'
            );

            return $result;
        }
        $topic = Topics::SHIPMENT_EXPORT_TO_DPD_STATION;
        if ($forced) {
            $topic = Topics::SHIPMENT_EXPORT_TO_DPD_STATION_FORCED;
        }
        $this->producer->send($topic, JSON::encode(['orderId' => $order->getId()]));
        $result['successful'] = true;

        return $result;
    }

    /**
     * Returns whether the admin enabled the station export or not in the integration settings
     */
    public function isStationExportEnabled(): bool
    {
        return $this->settingsProvider->getSettings()->get('dpd_fr_station_enabled') ?? false;
    }


    /**
     * Checks whether the order is a valid candidate for Station export or not
     */
    public function isOrderExportable(Order $order, bool $forced): bool
    {
        if (!DpdFranceShippingMethodProvider::isDpdFrShippingMethod($order->getShippingMethod())) {
            return false;
        }
        if ($forced) {
            return true;
        }
        if ($order->getSynchronizedDpd() !== null) {
            return false;
        }
        $internalStatusName = $order->getInternalStatus() ? strtolower(
            $order->getInternalStatus()->getName()
        ) : null;

        $exportableStatuses = explode(
            ',',
            $this->settingsProvider->getSettings()->get('dpd_fr_order_statuses_sent_to_station')
        );

        return (in_array($internalStatusName, $exportableStatuses, true));
    }
}
