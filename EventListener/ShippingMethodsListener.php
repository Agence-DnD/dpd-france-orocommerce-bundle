<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\EventListener;

use Dnd\Bundle\DpdFranceShippingBundle\Condition\ShippableWithDpdFrance;
use Dnd\Bundle\DpdFranceShippingBundle\Method\DpdFranceShippingMethodProvider;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\ShippingServiceProvider;
use Oro\Bundle\CheckoutBundle\Entity\Checkout;
use Oro\Bundle\SaleBundle\Entity\QuoteDemand;
use Oro\Bundle\ShippingBundle\Event\ApplicableMethodsEvent;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodViewCollection;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShippingMethodsListener
{
    public function __construct(
        private readonly ShippableWithDpdFrance $shippableWithDpdFranceCondition,
        private readonly ShippingServiceProvider $shippingServiceProvider
    ) {
    }

    /**
     * Ensures that DPD france shipping services meet conditions
     */
    public function enforceDpDFranceValidations(ApplicableMethodsEvent $event): void
    {
        $methodCollection = $event->getMethodCollection();
        $sourceEntity = $event->getSourceEntity();

        if (!$sourceEntity instanceof Checkout || $sourceEntity->getSourceEntity() instanceof QuoteDemand) {
            return;
        }

        foreach ($methodCollection->getAllMethodsTypesViews() as $shippingMethodName => $methodTypes) {
            if (DpdFranceShippingMethodProvider::isDpdFrShippingMethod($shippingMethodName)) {
                foreach ($methodTypes as $methodTypeId => $methodTypesView) {
                    $methodTypeView = $methodCollection->getMethodTypeView(
                        $shippingMethodName,
                        $methodTypeId
                    );
                    $methodTypeView['logo'] = $this->shippingServiceProvider->getShippingServiceLogo(
                        $methodTypeId
                    );
                    $methodTypeView['description'] = $this->shippingServiceProvider->getShippingServiceDesc(
                        $methodTypeId
                    );
                    $methodTypeView['name'] = $this->shippingServiceProvider->getShippingServiceLabel(
                        $methodTypeId
                    );
                    $methodCollection->removeMethodTypeView($shippingMethodName, $methodTypeId);
                    $methodCollection->addMethodTypeView($shippingMethodName, $methodTypeId, $methodTypeView);
                    if ($this->shippableWithDpdFranceCondition->isValid($methodTypesView, $sourceEntity) !== true) {
                        $methodCollection->removeMethodTypeView($shippingMethodName, $methodTypeId);
                    }
                }
            }
        }
    }
}
