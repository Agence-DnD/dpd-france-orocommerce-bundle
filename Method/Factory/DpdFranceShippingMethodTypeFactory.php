<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method\Factory;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Method\DpdFranceShippingMethodType;
use Dnd\Bundle\DpdFranceShippingBundle\Method\Identifier\DpdFranceMethodTypeIdentifierGenerator;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;

/**
 * Class DpdFranceShippingMethodTypeFactory
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Method\Factory
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethodTypeFactory
{
    /**
     * Description $typeIdentifierGenerator field
     *
     * @var DpdFranceMethodTypeIdentifierGenerator $typeIdentifierGenerator
     */
    private DpdFranceMethodTypeIdentifierGenerator $typeIdentifierGenerator;
    /**
     * Description $methodIdentifierGenerator field
     *
     * @var IntegrationIdentifierGeneratorInterface $methodIdentifierGenerator
     */
    private IntegrationIdentifierGeneratorInterface $methodIdentifierGenerator;

    /**
     * DpdFranceShippingMethodTypeFactory constructor
     *
     * @param DpdFranceMethodTypeIdentifierGenerator  $typeIdentifierGenerator
     * @param IntegrationIdentifierGeneratorInterface $methodIdentifierGenerator
     */
    public function __construct(
        DpdFranceMethodTypeIdentifierGenerator $typeIdentifierGenerator,
        IntegrationIdentifierGeneratorInterface $methodIdentifierGenerator
    ) {
        $this->typeIdentifierGenerator   = $typeIdentifierGenerator;
        $this->methodIdentifierGenerator = $methodIdentifierGenerator;
    }

    /**
     * Description create function
     *
     * @param Channel         $channel
     * @param ShippingService $service
     *
     * @return DpdFranceShippingMethodType
     */
    public function create(Channel $channel, ShippingService $service)
    {
        return new DpdFranceShippingMethodType(
            $this->getIdentifier($channel, $service),
            $this->getLabel($service),
            $this->methodIdentifierGenerator->generateIdentifier($channel),
            $this->getSettings($channel),
            $service,
        );
    }

    /**
     * @param ShippingService $service
     *
     * @return string
     */
    private function getLabel(ShippingService $service): string
    {
        return $service->getLabel();
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
     * @param Channel         $channel
     * @param ShippingService $service
     *
     * @return string
     */
    private function getIdentifier(Channel $channel, ShippingService $service): string
    {
        return $this->typeIdentifierGenerator->generateIdentifier($channel, $service);
    }
}
