<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\EventListener;

use Dnd\Bundle\DpdFranceShippingBundle\Condition\ShippableWithDpdFrance;
use Dnd\Bundle\DpdFranceShippingBundle\Integration\DpdFranceChannel;
use Oro\Bundle\CheckoutBundle\Entity\Checkout;
use Oro\Bundle\SaleBundle\Entity\QuoteDemand;
use Oro\Bundle\ShippingBundle\Event\ApplicableMethodsEvent;

/**
 * Class ShippingMethodsListener
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\EventListener
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShippingMethodsListener
{
    /**
     * Description $shippableWithDpdFranceCondition field
     *
     * @var ShippableWithDpdFrance $shippableWithDpdFranceCondition
     */
    protected ShippableWithDpdFrance $shippableWithDpdFranceCondition;

    /**
     * ShippingMethodsListener constructor
     *
     * @param ShippableWithDpdFrance $shippableWithDpdFranceCondition
     */
    public function __construct(ShippableWithDpdFrance $shippableWithDpdFranceCondition)
    {
        $this->shippableWithDpdFranceCondition = $shippableWithDpdFranceCondition;
    }

    /**
     * Ensures that DPD france shipping services meet conditions
     *
     * @param ApplicableMethodsEvent $event
     *
     * @return void
     */
    public function enforceDpDFranceValidations(ApplicableMethodsEvent $event): void
    {
        $methodCollection = $event->getMethodCollection();
        $sourceEntity     = $event->getSourceEntity();

        if (!$sourceEntity instanceof Checkout || $sourceEntity->getSourceEntity() instanceof QuoteDemand) {
            return;
        }
        foreach ($methodCollection->getAllMethodsTypesViews() as $shippingMethodName => $methodTypes) {
            if (str_contains($shippingMethodName, DpdFranceChannel::TYPE)) {
                foreach ($methodTypes as $methodTypeId => $methodTypesView) {
                    if ($this->shippableWithDpdFranceCondition->isValid($methodTypesView, $sourceEntity) !== true) {
                        $methodCollection->removeMethodTypeView($shippingMethodName, $methodTypeId);
                    }
                }
            }
        }
    }
}
