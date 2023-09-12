<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method\Factory;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Method\DpdFranceShippingMethodType;
use Dnd\Bundle\DpdFranceShippingBundle\Method\Identifier\DpdFranceMethodTypeIdentifierGenerator;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodTypeInterface;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethodTypeFactory
{
    public function __construct(
        private readonly DpdFranceMethodTypeIdentifierGenerator $typeIdentifierGenerator,
        private readonly IntegrationIdentifierGeneratorInterface $methodIdentifierGenerator
    ) {
    }

    public function create(Channel $channel, ShippingService $service): ShippingMethodTypeInterface
    {
        return new DpdFranceShippingMethodType(
            $this->getIdentifier($channel, $service),
            $this->getLabel($service),
            $this->methodIdentifierGenerator->generateIdentifier($channel),
            $this->getSettings($channel),
            $service,
        );
    }

    private function getLabel(ShippingService $service): string
    {
        return $service->getLabel();
    }

    private function getSettings(Channel $channel): DpdFranceTransportSettings
    {
        return $channel->getTransport();
    }

    private function getIdentifier(Channel $channel, ShippingService $service): string
    {
        return $this->typeIdentifierGenerator->generateIdentifier($channel, $service);
    }
}
