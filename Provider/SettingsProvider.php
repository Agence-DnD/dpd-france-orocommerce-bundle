<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Provider;

use Dnd\Bundle\DpdFranceShippingBundle\Integration\DpdFranceChannel;
use Dnd\Bundle\DpdFranceShippingBundle\Integration\DpdFranceTransport;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Entity\Repository\ChannelRepository;
use Oro\Bundle\IntegrationBundle\Provider\ConnectorContextMediator;
use Psr\Log\InvalidArgumentException;
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
     * @throws InvalidArgumentException
     */
    public function getSettings(): ParameterBag
    {
        if (null === $this->settings) {
            /** @var DpdFranceTransport $transport */
            $transport      = $this->contextMediator->getInitializedTransport($this->getDpdFranceChannel(), true);
            $this->settings = $transport->getSettings();
        }

        return $this->settings;
    }

    /**
     * Description getDpdFranceChannel function
     *
     * @return Channel
     * @throws InvalidArgumentException
     */
    private function getDpdFranceChannel(): Channel
    {
        if (null === $this->channel) {
            $this->channel = $this->channelRepository->findOneBy(['type' => DpdFranceChannel::TYPE]);
            if (null === $this->channel) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Please create integration of type %s, while using DPD France module.',
                        DpdFranceChannel::TYPE
                    )
                );
            }
        }

        return $this->channel;
    }
}
