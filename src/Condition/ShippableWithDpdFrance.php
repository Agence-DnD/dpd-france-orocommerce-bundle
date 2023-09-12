<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Condition;

use Dnd\Bundle\DpdFranceShippingBundle\Exception\PackageException;
use Dnd\Bundle\DpdFranceShippingBundle\Factory\PackageFactory;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\ShippingServiceProvider;
use Oro\Bundle\CheckoutBundle\Entity\Checkout;
use Oro\Bundle\CheckoutBundle\Provider\CheckoutShippingContextProvider;
use Oro\Bundle\SaleBundle\Entity\QuoteDemand;
use Oro\Bundle\ShippingBundle\Context\ShippingLineItemInterface;
use Psr\Log\LoggerInterface;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShippableWithDpdFrance
{
    public function __construct(
        private readonly CheckoutShippingContextProvider $checkoutShippingContextProvider,
        private readonly PackageFactory $packageFactory,
        private readonly LoggerInterface $logger,
        private readonly ShippingServiceProvider $shippingServiceProvider
    ) {
    }

    /**
     * Checks whether the checkout is eligible for DPD France shipment
     */
    public function isValid(array $methodTypeView, Checkout | QuoteDemand $context): bool
    {
        $shippingService = $this->shippingServiceProvider->getServiceForMethodTypeIdentifier(
            $methodTypeView['identifier']
        );
        $shippingContext = $this->checkoutShippingContextProvider->getContext($context);

        if ($shippingContext === null || $shippingService === null) {
            return false;
        }

        /** @var ShippingLineItemInterface[] $packages */
        try {
            $packages = $this->packageFactory->create(
                $shippingContext->getLineItems(),
                $shippingService,
                $context->getWebsite()?->getId()
            );
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
