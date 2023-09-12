<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Provider;

use Dnd\Bundle\DpdFranceShippingBundle\Integration\DpdFranceChannel;
use Dnd\Bundle\DpdFranceShippingBundle\Integration\DpdFranceTransport;
use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\IntegrationBundle\Entity\Channel as Integration;
use Oro\Bundle\IntegrationBundle\Provider\ConnectorContextMediator;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class SettingsProvider
{
    private ?Integration $channel = null;
    protected ?ParameterBag $settings = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ConnectorContextMediator $contextMediator
    ) {
    }

    public function getSettings(): ParameterBag
    {
        $channel = $this->getDpdFranceChannel();
        if (null === $this->settings) {
            $this->settings = new ParameterBag();
            if ($channel !== null) {
                /** @var DpdFranceTransport $transport */
                $transport = $this->contextMediator->getInitializedTransport($channel, true);
                $this->settings = $transport->getSettings();
            }
        }

        return $this->settings;
    }

    private function getDpdFranceChannel(): ?Integration
    {
        if (null === $this->channel) {
            $this->channel = $this->entityManager->getRepository(Integration::class)->findOneBy(['type' => DpdFranceChannel::TYPE]);
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
