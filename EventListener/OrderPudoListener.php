<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\EventListener;

use Dnd\Bundle\DpdFranceShippingBundle\Provider\PudoProvider;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\WorkflowBundle\Exception\WorkflowException;
use Oro\Component\Action\Event\ExtendableActionEvent;

/**
 * Sets DPD relay related data into the order
 *
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class OrderPudoListener
{
    public function __construct(
        private readonly PudoProvider $pudoProvider
    ) {
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        /** @var mixed|Order $entity */
        $entity = $event->getObject();
        if (!($entity instanceof Order)) {
            return;
        }
        if ($event->hasChangedField('dpd_fr_relay_id')) {
            $this->updateDpdFields($entity);
        }
    }

    /**
     * @throws WorkflowException
     */
    public function onCreateOrder(ExtendableActionEvent $event): void
    {
        if (!$this->isCorrectOrderContext($event->getContext())) {
            return;
        }
        $this->updateDpdFields($event->getContext()?->getData()?->get('order'));
    }

    /**
     * @throws WorkflowException
     */
    protected function isCorrectOrderContext($context): bool
    {
        return $context?->getData()?->get('order') instanceof Order;
    }

    private function updateDpdFields(Order $order): void
    {
        if ($order->getDpdFrRelayId() === -1) {
            $order->setDpdFrRelayId(null);
        }
        $pudoName = '';
        if ($order->getDpdFrRelayId() !== null) {
            try {
                /** @var \SimpleXMLElement $pudoObject */
                $response = $this->pudoProvider->getPudoDetails((string)$order->getDpdFrRelayId());
                $pudoName = $response->PUDO_ITEMS->PUDO_ITEM->NAME->__toString() ?? '';
            } catch (\Throwable $e) {
                $pudoName = '';
            }
        }
        if ($order->getDeliveryPhone() === '0') {
            $order->setDeliveryPhone(null);
        }
        $order->setDpdFrRelayName($pudoName);
    }
}
