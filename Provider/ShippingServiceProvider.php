<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Provider;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Doctrine\ORM\EntityRepository;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

/**
 * Class ShippingServiceProvider
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Provider
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShippingServiceProvider
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
     * @var array $dpdFrShippingServices
     */
    protected array $dpdFrShippingServices;
    /**
     * Description $settingsProvider field
     *
     * @var SettingsProvider $settingsProvider
     */
    protected SettingsProvider $settingsProvider;

    /**
     * ShippingServiceProvider constructor
     *
     * @param DoctrineHelper   $doctrineHelper
     * @param SettingsProvider $settingsProvider
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        SettingsProvider $settingsProvider
    ) {
        $this->doctrineHelper = $doctrineHelper;
        $this->settingsProvider = $settingsProvider;
    }

    /**
     * Retrieves the corresponding dpd France shipping service for a given methodTypeView
     *
     * @param string $identifier
     *
     * @return ShippingService|null
     */
    public function getServiceForMethodTypeIdentifier(string $identifier): ?ShippingService
    {
        /** @var  ShippingService[] $services */
        $services = $this->getDpdFrShippingServices();

        return $services[$identifier] ?? null;
    }

    /**
     * Retrieves the dpd France shipping services from DB
     *
     * @return array|ShippingService[]
     */
    private function getDpdFrShippingServices(): array
    {
        if (empty($this->dpdFrShippingServices)) {
            /** @var EntityRepository $dpdFrServicesRepository */
            $dpdFrServicesRepository     = $this->doctrineHelper->getEntityRepositoryForClass(ShippingService::class);
            $this->dpdFrShippingServices = $dpdFrServicesRepository->findAll();

            /** @var ShippingService[] $shippingServices */
            $shippingServices = $dpdFrServicesRepository->findAll();
            /** @var ShippingService $shippingService */
            foreach ($shippingServices as $shippingService) {
                $this->dpdFrShippingServices[$shippingService->getCode()] = $shippingService;
            }
        }

        return $this->dpdFrShippingServices;
    }

    /**
     * Description getShippingServiceLogo function
     *
     * @param string $identifier
     *
     * @return string
     */
    public function getShippingServiceLogo(string $identifier): string
    {
        $service = $this->getServiceForMethodTypeIdentifier($identifier);

        return $service !== null ? $service->getIcon() : '';
    }

    /**
     * Description getShippingServiceLabel function
     *
     * @param string $identifier
     *
     * @return string
     */
    public function getShippingServiceLabel(string $identifier): string
    {
        return $this->settingsProvider->getServiceLabel($identifier) ?? '';
    }

    /**
     * Description getShippingServiceDesc function
     *
     * @param string $identifier
     *
     * @return string
     */
    public function getShippingServiceDesc(string $identifier): string
    {
        return $this->settingsProvider->getServiceDesc($identifier) ?? '';
    }
}
