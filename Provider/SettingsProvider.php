<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Provider;

use Dnd\Bundle\DpdFranceShippingBundle\Integration\DpdFranceChannel;
use Dnd\Bundle\DpdFranceShippingBundle\Integration\DpdFranceTransport;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Entity\Repository\ChannelRepository;
use Oro\Bundle\IntegrationBundle\Provider\ConnectorContextMediator;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class SettingsProvider
 *
 * @package   SettingsProvider
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class SettingsProvider
{
    /**
     * Description $channel field
     *
     * @var Channel|null $channel
     */
    private ?Channel $channel = null;
    /**
     * Description $settings field
     *
     * @var ParameterBag|null $settings
     */
    protected ?ParameterBag $settings = null;
    /**
     * Description $channelRepository field
     *
     * @var ChannelRepository $channelRepository
     */
    private ChannelRepository $channelRepository;
    /**
     * Description $contextMediator field
     *
     * @var ConnectorContextMediator $contextMediator
     */
    private ConnectorContextMediator $contextMediator;

    /**
     * SettingsProvider constructor
     *
     * @param ChannelRepository        $channelRepository
     * @param ConnectorContextMediator $contextMediator
     */
    public function __construct(
        ChannelRepository $channelRepository,
        ConnectorContextMediator $contextMediator
    ) {
        $this->channelRepository = $channelRepository;
        $this->contextMediator   = $contextMediator;
    }

    /**
     * Description getSettings function
     *
     * @return ParameterBag
     */
    public function getSettings(): ParameterBag
    {
        /** @var Channel|null $channel */
        $channel = $this->getDpdFranceChannel();
        if (null === $this->settings) {
            $this->settings = new ParameterBag();
            if ($channel !== null) {
                /** @var DpdFranceTransport $transport */
                $transport      = $this->contextMediator->getInitializedTransport($channel, true);
                $this->settings = $transport->getSettings();
            }
        }

        return $this->settings;
    }

    /**
     * Description getDpdFranceChannel function
     *
     * @return Channel|null
     */
    private function getDpdFranceChannel(): ?Channel
    {
        if (null === $this->channel) {
            $this->channel = $this->channelRepository->findOneBy(['type' => DpdFranceChannel::TYPE]);
        }

        return $this->channel;
    }

    /**
     * Description getServiceLabel function
     *
     * @param string $serviceIdentifier
     *
     * @return string|null
     */
    public function getServiceLabel(string $serviceIdentifier): ?string
    {
        return $this->getSettings()->get($serviceIdentifier . '_method_name');
    }

    /**
     * Description getServiceDesc function
     *
     * @param string $serviceIdentifier
     *
     * @return string|null
     */
    public function getServiceDesc(string $serviceIdentifier): ?string
    {
        return $this->getSettings()->get($serviceIdentifier . '_method_desc');
    }
}
