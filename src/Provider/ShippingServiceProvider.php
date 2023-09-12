<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Provider;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShippingServiceProvider
{
    protected array $dpdFrShippingServices;

    /**
     * ShippingServiceProvider constructor
     *
     * @param DoctrineHelper $doctrineHelper
     * @param SettingsProvider $settingsProvider
     */
    public function __construct(
        private readonly DoctrineHelper $doctrineHelper,
        private readonly SettingsProvider $settingsProvider
    ) {
    }

    /**
     * Retrieves the corresponding dpd France shipping service for a given methodTypeView
     */
    public function getServiceForMethodTypeIdentifier(string $identifier): ?ShippingService
    {
        $services = $this->getDpdFrShippingServices();

        return $services[$identifier] ?? null;
    }

    /**
     * Retrieves the dpd France shipping services from DB
     *
     * @return ShippingService[]
     */
    private function getDpdFrShippingServices(): array
    {
        if (empty($this->dpdFrShippingServices)) {
            $dpdFrServicesRepository = $this->doctrineHelper->getEntityRepositoryForClass(ShippingService::class);
            $this->dpdFrShippingServices = $dpdFrServicesRepository->findAll();
            $shippingServices = $dpdFrServicesRepository->findAll();
            foreach ($shippingServices as $shippingService) {
                $this->dpdFrShippingServices[$shippingService->getCode()] = $shippingService;
            }
        }

        return $this->dpdFrShippingServices;
    }

    public function getShippingServiceLogo(string $identifier): string
    {
        $service = $this->getServiceForMethodTypeIdentifier($identifier);

        return $service !== null ? $service->getIcon() : '';
    }

    public function getShippingServiceLabel(string $identifier): string
    {
        return $this->settingsProvider->getServiceLabel($identifier) ?? '';
    }

    public function getShippingServiceDesc(string $identifier): string
    {
        return $this->settingsProvider->getServiceDesc($identifier) ?? '';
    }
}
