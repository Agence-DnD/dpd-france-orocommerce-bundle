<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Condition;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\PackageException;
use Dnd\Bundle\DpdFranceShippingBundle\Factory\PackageFactory;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\ShippingServiceProvider;
use Oro\Bundle\CheckoutBundle\Entity\Checkout;
use Oro\Bundle\CheckoutBundle\Provider\CheckoutShippingContextProvider;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\SaleBundle\Entity\QuoteDemand;
use Oro\Bundle\ShippingBundle\Context\ShippingLineItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ShippableWithDpdFrance
 *
 * @package   ShippableWithDpdFrance
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShippableWithDpdFrance
{
    /**
     * Description $doctrineHelper field
     *
     * @var DoctrineHelper $doctrineHelper
     */
    protected DoctrineHelper $doctrineHelper;
    /**
     * Description $checkoutShippingContextProvider field
     *
     * @var CheckoutShippingContextProvider $checkoutShippingContextProvider
     */
    protected CheckoutShippingContextProvider $checkoutShippingContextProvider;
    /**
     * Description $packageFactory field
     *
     * @var PackageFactory $packageFactory
     */
    protected PackageFactory $packageFactory;
    /**
     * Description $logger field
     *
     * @var LoggerInterface $logger
     */
    protected LoggerInterface $logger;
    /**
     * Description $shippingServiceProvider field
     *
     * @var ShippingServiceProvider $shippingServiceProvider
     */
    protected ShippingServiceProvider $shippingServiceProvider;

    /**
     * ShippableWithDpdFrance constructor
     *
     * @param DoctrineHelper                  $doctrineHelper
     * @param CheckoutShippingContextProvider $checkoutShippingContextProvider
     * @param PackageFactory                  $packageFactory
     * @param LoggerInterface                 $logger
     * @param ShippingServiceProvider         $shippingServiceProvider
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        CheckoutShippingContextProvider $checkoutShippingContextProvider,
        PackageFactory $packageFactory,
        LoggerInterface $logger,
        ShippingServiceProvider $shippingServiceProvider

    ) {
        $this->doctrineHelper                  = $doctrineHelper;
        $this->checkoutShippingContextProvider = $checkoutShippingContextProvider;
        $this->packageFactory                  = $packageFactory;
        $this->logger                          = $logger;
        $this->shippingServiceProvider         = $shippingServiceProvider;
    }

    /**
     * Checks whether the checkout is eligible for DPD France shipment
     *>
     *
     * @param mixed[]              $methodTypeView
     * @param Checkout|QuoteDemand $context
     *
     * @return bool
     */
    public function isValid(array $methodTypeView, $context): bool
    {
        /** @var ShippingService $shippingService */
        $shippingService = $this->shippingServiceProvider->getServiceForMethodTypeIdentifier(
            $methodTypeView['identifier']
        );
        $shippingContext = $this->checkoutShippingContextProvider->getContext($context);

        if ($shippingContext === null) {
            return false;
        }

        /** @var ShippingLineItemInterface[] $packages */
        try {
            $packages = $this->packageFactory->create($shippingContext->getLineItems(), $shippingService);
        } catch (PackageException $e) {
            $this->logger->info(
                sprintf(
                    'Package exception for %s with ID: %s.',
                    get_class($shippingContext->getSourceEntity()),
                    $shippingContext->getSourceEntityIdentifier()
                )
            );
            $this->logger->info($e->getMessage());

            return false;
        }

        return !empty($packages);
    }
}
