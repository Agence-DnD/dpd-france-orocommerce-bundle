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
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethodFactory implements IntegrationShippingMethodFactoryInterface
{
    protected IntegrationIdentifierGeneratorInterface $integrationIdentifierGenerator;

    public function __construct(
        private readonly IntegrationIconProviderInterface $integrationIconProvider,
        private readonly IntegrationIdentifierGeneratorInterface $methodIdentifierGenerator,
        private readonly DpdFranceShippingMethodTypeFactory $methodTypeFactory
    ) {
    }

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

    private function createTypes(Channel $channel): array
    {
        $applicableShippingServices = $this->getSettings($channel)->getShippingServices()->toArray();

        return array_map(function (ShippingService $shippingService) use ($channel) {
            return $this->methodTypeFactory->create($channel, $shippingService);
        }, $applicableShippingServices);
    }

    private function getSettings(Channel $channel): DpdFranceTransportSettings
    {
        return $channel->getTransport();
    }

    private function getIdentifier(Channel $channel): string
    {
        return $this->methodIdentifierGenerator->generateIdentifier($channel);
    }

    private function getIcon(Channel $channel): ?string
    {
        return $this->integrationIconProvider->getIcon($channel);
    }

    private function getLabel(Channel $channel): string
    {
        return DpdFranceShippingMethod::LABEL;
    }
}
