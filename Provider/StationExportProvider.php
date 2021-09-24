<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Provider;

use Dnd\Bundle\DpdFranceShippingBundle\Async\Topics;
use Dnd\Bundle\DpdFranceShippingBundle\Method\DpdFranceShippingMethodProvider;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Oro\Component\MessageQueue\Transport\Exception\Exception;
use Oro\Component\MessageQueue\Util\JSON;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class StationExportProvider
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Provider
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class StationExportProvider
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
     * Description $translator field
     *
     * @var TranslatorInterface $translator
     */
    protected TranslatorInterface $translator;

    /**
     * OrderListener constructor
     *
     * @param LoggerInterface          $logger
     * @param MessageProducerInterface $producer
     * @param SettingsProvider         $settingsProvider
     * @param TranslatorInterface      $translator
     */
    public function __construct(
        LoggerInterface $logger,
        MessageProducerInterface $producer,
        SettingsProvider $settingsProvider,
        TranslatorInterface $translator
    ) {
        $this->logger           = $logger;
        $this->producer         = $producer;
        $this->settingsProvider = $settingsProvider;
        $this->translator       = $translator;
    }

    /**
     * Enqueues an order for DPD FR station export if elligible
     *
     * @param Order     $order
     * @param bool|null $forced
     *
     * @return mixed[]
     * @throws Exception
     */
    public function queueIfExportable(Order $order, ?bool $forced = false): array
    {
        /** @var mixed[] $result */
        $result = [
            'successful' => false,
            'error'      => [],
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
        /** @var string $topic */
        $topic = Topics::SHIPMENT_EXPORT_TO_DPD_STATION;
        if ($forced) {
            $topic = Topics::SHIPMENT_EXPORT_TO_DPD_STATION_FORCED;
        }
        $this->producer->send($topic, JSON::encode(['orderId' => $order->getId()]));
        $result['successful'] = true;

        return $result;
    }

    /**
     * Checks whether the order is a valid candidate for Station export or not
     *
     * @param Order $order
     * @param bool  $forced
     *
     * @return bool
     */
    public function isOrderExportable(Order $order, bool $forced): bool
    {
        if ($order->getSynchronizedDpd() === null && DpdFranceShippingMethodProvider::isDpdFrShippingMethod(
                $order->getShippingMethod()
            )) {
            if ($forced) {
                return true;
            }
            /** @var string|null $internalStatusName */
            $internalStatusName = $order->getInternalStatus() ? strtolower(
                $order->getInternalStatus()->getName()
            ) : null;

            /** @var false|string[] $exportableStatuses */
            $exportableStatuses = explode(
                ',',
                $this->settingsProvider->getSettings()->get('dpd_fr_order_statuses_sent_to_station')
            );

            return (false !== $exportableStatuses && in_array($internalStatusName, $exportableStatuses, true));
        }

        return false;
    }
}
