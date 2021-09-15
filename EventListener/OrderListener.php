<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\EventListener;

use Dnd\Bundle\DpdFranceShippingBundle\Async\Topics;
use Dnd\Bundle\DpdFranceShippingBundle\Method\DpdFranceShippingMethodProvider;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\SettingsProvider;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Oro\Component\MessageQueue\Transport\Exception\Exception;
use Oro\Component\MessageQueue\Util\JSON;
use Psr\Log\LoggerInterface;

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
     * Description $logger field
     *
     * @var LoggerInterface $logger
     */
    protected LoggerInterface $logger;
    /**
     * Description $producer field
     *
     * @var MessageProducerInterface $producer
     */
    protected MessageProducerInterface $producer;
    /**
     * Description $settingsProvider field
     *
     * @var SettingsProvider $settingsProvider
     */
    protected SettingsProvider $settingsProvider;

    /**
     * OrderListener constructor
     *
     * @param LoggerInterface          $logger
     * @param MessageProducerInterface $producer
     * @param SettingsProvider         $settingsProvider
     */
    public function __construct(
        LoggerInterface $logger,
        MessageProducerInterface $producer,
        SettingsProvider $settingsProvider
    ) {
        $this->logger           = $logger;
        $this->producer         = $producer;
        $this->settingsProvider = $settingsProvider;
    }

    /**
     * Whenever a DPD FR Shipped order is persisted with the right status, it needs to be queued for Station export
     *
     * @param Order $order
     *
     * @return void
     * @throws Exception
     */
    public function postUpdate(Order $order): void
    {
        try {
            if (!$this->isOrderExportable($order)) {
                return;
            }
        } catch (\InvalidArgumentException $e) {
            //No integration exists for DPD France
            return;
        }

        /** @var string $topic */
        $topic = Topics::SHIPMENT_EXPORT_TO_DPD_STATION;

        // check whether synchronized_dpd flag is set or not - really needed ? @todo
        if ($order->getSynchronizedDpd() !== null) {
            $topic = Topics::SHIPMENT_EXPORT_TO_DPD_STATION_FORCED;
        }
        $this->producer->send($topic, JSON::encode(['orderId' => $order->getId()]));
    }

    /**
     * Checks whether the order is a valid candidate for Station export or not
     *
     * @param Order $order
     *
     * @return bool
     */
    public function isOrderExportable(Order $order): bool
    {
        /** @var string|null $internalStatusName */
        $internalStatusName = $order->getInternalStatus() ? strtolower($order->getInternalStatus()->getName()) : null;

        /** @var false|string[] $exportableStatuses */
        $exportableStatuses = explode(
            ',',
            $this->settingsProvider->getSettings()->get('dpd_fr_order_statuses_sent_to_station')
        );
        if (false !== $exportableStatuses && in_array($internalStatusName, $exportableStatuses, true)) {
            return DpdFranceShippingMethodProvider::isDpdFrShippingMethod($order->getShippingMethod());
        }

        return false;
    }
}
