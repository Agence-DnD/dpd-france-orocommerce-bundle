<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Condition;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\PackageException;
use Dnd\Bundle\DpdFranceShippingBundle\Factory\PackageFactory;
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
     * Description $dpdFrShippingServices field
     *
     * @var ShippingService[] $dpdFrShippingServices
     */
    protected array $dpdFrShippingServices = [];
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
     * ShippingMethodsListener constructor
     *
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        CheckoutShippingContextProvider $checkoutShippingContextProvider,
        PackageFactory $packageFactory,
        LoggerInterface $logger

    ) {
        $this->doctrineHelper                  = $doctrineHelper;
        $this->checkoutShippingContextProvider = $checkoutShippingContextProvider;
        $this->packageFactory                  = $packageFactory;
        $this->logger = $logger;
    }

    /**
     * Checks
     *
     * @param mixed[]              $methodTypeView
     * @param Checkout|QuoteDemand $context
     *
     * @return bool
     */
    public function isValid(array $methodTypeView, $context): bool
    {
        /** @var ShippingService $shippingService */
        $shippingService = $this->getServiceForMethodTypeView($methodTypeView);
        $shippingContext = $this->checkoutShippingContextProvider->getContext($context);

        /** @var ShippingLineItemInterface[] $packages */
        try {
            $packages = $this->packageFactory->create($shippingContext->getLineItems(), $shippingService);
            return !empty($packages);
        } catch (PackageException $e) {
            // @todo log that somewhere in the order to give some insights to customer service
            $this->logger->info('PACKAGE EXCEPTION');
            $this->logger->info($e->getMessage());
            return false;
        } catch (\Exception $e) {
            $this->logger->error('UNKNOWN EXCEPTION');
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * Description getServiceForMethodTypeView function
     *
     * @param array $methodTypeView
     *
     * @return ShippingService|null
     */
    private function getServiceForMethodTypeView(array $methodTypeView): ?ShippingService
    {
        $services = $this->getDpdFrShippingServices();

        return $services[$methodTypeView['identifier']] ?? null;
    }

    /**
     * Description getDpdFrShippingServices function
     *
     * @return array|ShippingService[]
     */
    private function getDpdFrShippingServices(): array
    {
        if (empty($this->dpdFrShippingServices)) {
            $dpdFrServicesRepository     = $this->doctrineHelper->getEntityRepositoryForClass(ShippingService::class);
            $this->dpdFrShippingServices = $dpdFrServicesRepository->findAll();

            /** @var ShippingService[] $shippingServices */
            $shippingServices = $dpdFrServicesRepository->findAll();
            foreach ($shippingServices as $shippingService) {
                $this->dpdFrShippingServices[$shippingService->getCode()] = $shippingService;
            }
        }

        return $this->dpdFrShippingServices;
    }
}
