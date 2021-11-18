<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method\Factory;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Method\DpdFranceShippingMethod;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\IntegrationBundle\Provider\IntegrationIconProviderInterface;
use Oro\Bundle\ShippingBundle\Method\Factory\IntegrationShippingMethodFactoryInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodInterface;

/**
 * Class DpdFranceShippingMethodFactory
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Method\Factory
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethodFactory implements IntegrationShippingMethodFactoryInterface
{
    /**
     * Description $integrationIdentifierGenerator field
     *
     * @var IntegrationIdentifierGeneratorInterface $integrationIdentifierGenerator
     */
    protected IntegrationIdentifierGeneratorInterface $integrationIdentifierGenerator;
    /**
     * Description $methodTypeFactory field
     *
     * @var DpdFranceShippingMethodTypeFactory $methodTypeFactory
     */
    protected DpdFranceShippingMethodTypeFactory $methodTypeFactory;
    /**
     * Description $methodIdentifierGenerator field
     *
     * @var IntegrationIdentifierGeneratorInterface $methodIdentifierGenerator
     */
    private IntegrationIdentifierGeneratorInterface $methodIdentifierGenerator;
    /**
     * Description $integrationIconProvider field
     *
     * @var IntegrationIconProviderInterface $integrationIconProvider
     */
    private IntegrationIconProviderInterface $integrationIconProvider;

    /**
     * DpdFranceShippingMethodFactory constructor
     *
     * @param IntegrationIconProviderInterface        $integrationIconProvider
     * @param IntegrationIdentifierGeneratorInterface $methodIdentifierGenerator
     * @param DpdFranceShippingMethodTypeFactory      $methodTypeFactory
     */
    public function __construct(
        IntegrationIconProviderInterface $integrationIconProvider,
        IntegrationIdentifierGeneratorInterface $methodIdentifierGenerator,
        DpdFranceShippingMethodTypeFactory $methodTypeFactory
    ) {
        $this->methodIdentifierGenerator = $methodIdentifierGenerator;
        $this->integrationIconProvider   = $integrationIconProvider;
        $this->methodTypeFactory         = $methodTypeFactory;
    }

    /**
     * Description create function
     *
     * @param Channel $channel
     *
     * @return ShippingMethodInterface
     */
    public function create(Channel $channel): ShippingMethodInterface
    {
        return new DpdFranceShippingMethod(
            $this->getIdentifier($channel),
            $this->getLabel($channel),
            $this->getIcon($channel),
            $this->createTypes($channel),
            $this->getSettings($channel),
            $channel->isEnabled()
        );
    }

    /**
     * @param Channel $channel
     *
     * @return array
     */
    private function createTypes(Channel $channel): array
    {
        $applicableShippingServices = $this->getSettings($channel)->getShippingServices()->toArray();

        return array_map(function (ShippingService $shippingService) use ($channel) {
            return $this->methodTypeFactory->create($channel, $shippingService);
        }, $applicableShippingServices);
    }

    /**
     * @param Channel $channel
     *
     * @return DpdFranceTransportSettings
     */
    private function getSettings(Channel $channel): DpdFranceTransportSettings
    {
        return $channel->getTransport();
    }

    /**
     * @param Channel $channel
     *
     * @return string
     */
    private function getIdentifier(Channel $channel): string
    {
        return $this->methodIdentifierGenerator->generateIdentifier($channel);
    }

    /**
     * @param Channel $channel
     *
     * @return string|null
     */
    private function getIcon(Channel $channel): ?string
    {
        return $this->integrationIconProvider->getIcon($channel);
    }

    /**
     * @param Channel $channel
     *
     * @return string
     */
    private function getLabel(Channel $channel): string
    {
        return DpdFranceShippingMethod::LABEL;
    }
}
