<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method\Factory;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Method\DpdFranceShippingMethod;
use Dnd\Bundle\DpdFranceShippingBundle\Method\Type\DpdClassicShippingMethodType;
use Dnd\Bundle\DpdFranceShippingBundle\Method\Type\DpdPredictShippingMethodType;
use Dnd\Bundle\DpdFranceShippingBundle\Method\Type\DpdRelayShippingMethodType;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\IntegrationBundle\Provider\IntegrationIconProviderInterface;
use Oro\Bundle\ShippingBundle\Method\Factory\IntegrationShippingMethodFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DpdFranceShippingMethodFromChannelFactory
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Method\Factory
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethodFromChannelFactory implements IntegrationShippingMethodFactoryInterface
{
    /**
     * @var IntegrationIdentifierGeneratorInterface
     */
    private $identifierGenerator;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var IntegrationIconProviderInterface
     */
    private $integrationIconProvider;

    /**
     * DpdFranceShippingMethodFromChannelFactory constructor
     *
     * @param IntegrationIdentifierGeneratorInterface $identifierGenerator
     * @param TranslatorInterface                     $translator
     * @param IntegrationIconProviderInterface        $integrationIconProvider
     */
    public function __construct(
        IntegrationIdentifierGeneratorInterface $identifierGenerator,
        TranslatorInterface $translator,
        IntegrationIconProviderInterface $integrationIconProvider
    ) {
        $this->identifierGenerator     = $identifierGenerator;
        $this->translator              = $translator;
        $this->integrationIconProvider = $integrationIconProvider;
    }

    /**
     * Description create function
     *
     * @param Channel $channel
     *
     * @return DpdFranceShippingMethod
     */
    public function create(Channel $channel)
    {
        $id    = $this->identifierGenerator->generateIdentifier($channel);
        $label = $this->getChannelLabel($channel);
        $icon  = $this->getIcon($channel);
        $types = $this->createTypes($channel);

        return new DpdFranceShippingMethod($id, $label, $icon, $channel->isEnabled(), $types);
    }

    /**
     * Description getChannelLabel function
     *
     * @param Channel $channel
     *
     * @return string
     */
    private function getChannelLabel(Channel $channel): string
    {
        $settings = $this->getSettings($channel);

        return (string)$settings->getLabel('classic'); //@fixme
    }

    /**
     * @param Channel $channel
     *
     * @return DpdFranceTransportSettings
     */
    private function getSettings(Channel $channel)
    {
        return $channel->getTransport();
    }

    /**
     * Description getIcon function
     *
     * @param Channel $channel
     *
     * @return string|null
     */
    private function getIcon(Channel $channel): ?string
    {
        return $this->integrationIconProvider->getIcon($channel);
    }

    /**
     * Description createTypes function
     *
     * @param Channel $channel
     *
     * @return array
     */
    private function createTypes(Channel $channel): array
    {
        /* @var DpdFranceTransportSettings $settings */
        $settings     = $this->getSettings($channel);

        $classicLabel = (string) $settings->getLabel(DpdClassicShippingMethodType::IDENTIFIER);
        $predictLabel = (string) $settings->getLabel(DpdPredictShippingMethodType::IDENTIFIER);
        $relayLabel   = (string) $settings->getLabel(DpdRelayShippingMethodType::IDENTIFIER);

        $classic = new DpdClassicShippingMethodType(DpdClassicShippingMethodType::IDENTIFIER, $classicLabel, $settings);
        $predict = new DpdPredictShippingMethodType(DpdPredictShippingMethodType::IDENTIFIER, $predictLabel, $settings);
        $relay   = new DpdRelayShippingMethodType(DpdRelayShippingMethodType::IDENTIFIER, $relayLabel, $settings);

        return [
            $classic->getIdentifier() => $classic,
            $predict->getIdentifier() => $predict,
            $relay->getIdentifier()   => $relay,
        ];
    }
}
