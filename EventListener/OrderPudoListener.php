<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\EventListener;

use Dnd\Bundle\DpdFranceShippingBundle\Provider\PudoProvider;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Oro\Bundle\OrderBundle\Entity\Order;

/**
 * Class OrderPudoListener
 *
 * @package   Dnd\Commerce\Bundle\OrderBundle\EventListener\Flux
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class OrderPudoListener
{
    /**
     * Description $pudoProvider field
     *
     * @var PudoProvider $pudoProvider
     */
    protected PudoProvider $pudoProvider;

    /**
     * OrderPudoListener constructor
     *
     * @param PudoProvider $pudoProvider
     */
    public function __construct(
        PudoProvider $pudoProvider
    ) {
        $this->pudoProvider = $pudoProvider;
    }

    /**
     * Description preUpdate function
     *
     * @param PreUpdateEventArgs $event
     */
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
     * Description preUpdate function
     *
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        /** @var mixed|Order $entity */
        $entity = $event->getObject();
        if (!($entity instanceof Order)) {
            return;
        }
        $this->updateDpdFields($entity);
    }

    /**
     * Description updateDpdFields function
     *
     * @param Order $order
     *
     * @return void
     */
    private function updateDpdFields(Order $order)
    {
        /** @var string $pudoName */
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
        $order->setDpdFrRelayName($pudoName);
    }
}
