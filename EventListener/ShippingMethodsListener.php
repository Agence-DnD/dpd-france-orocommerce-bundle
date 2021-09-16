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
     * Description $shippingServiceProvider field
     *
     * @var ShippingServiceProvider $shippingServiceProvider
     */
    protected ShippingServiceProvider $shippingServiceProvider;

    /**
     * ShippingMethodsListener constructor
     *
     * @param ShippableWithDpdFrance  $shippableWithDpdFranceCondition
     * @param ShippingServiceProvider $shippingServiceProvider
     */
    public function __construct(
        ShippableWithDpdFrance $shippableWithDpdFranceCondition,
        ShippingServiceProvider $shippingServiceProvider
    ) {
        $this->shippableWithDpdFranceCondition = $shippableWithDpdFranceCondition;
        $this->shippingServiceProvider         = $shippingServiceProvider;
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
        /** @var ShippingMethodViewCollection $methodCollection */
        $methodCollection = $event->getMethodCollection();

        /** @var Checkout|QuoteDemand $methodCollection */
        $sourceEntity = $event->getSourceEntity();

        if (!$sourceEntity instanceof Checkout || $sourceEntity->getSourceEntity() instanceof QuoteDemand) {
            return;
        }

        /**
         * @var string  $shippingMethodName
         * @var mixed[] $methodTypes
         */
        foreach ($methodCollection->getAllMethodsTypesViews() as $shippingMethodName => &$methodTypes) {
            if (DpdFranceShippingMethodProvider::isDpdFrShippingMethod($shippingMethodName)) {
                /**
                 * @var string  $methodTypeId
                 * @var mixed[] $methodTypesView
                 */
                foreach ($methodTypes as $methodTypeId => $methodTypesView) {
                    /** @var mixed[] $methodTypeView */
                    $methodTypeView                = $methodCollection->getMethodTypeView(
                        $shippingMethodName,
                        $methodTypeId
                    );
                    $methodTypeView['logo']        = $this->shippingServiceProvider->getShippingServiceLogo(
                        $methodTypeId
                    );
                    $methodTypeView['description'] = $this->shippingServiceProvider->getShippingServiceDesc(
                        $methodTypeId
                    );
                    $methodTypeView['name']        = $this->shippingServiceProvider->getShippingServiceLabel(
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
