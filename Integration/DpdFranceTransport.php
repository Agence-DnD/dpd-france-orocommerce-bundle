<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Integration;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransport as DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Form\Type\DpdFranceTransportSettingsType;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class DpdFranceTransport implements TransportInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface           $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'oro.dpd.transport.label';
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsFormType()
    {
        return DpdFranceTransportSettingsType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsEntityFQCN()
    {
        return DpdFranceTransportSettings::class;
    }

    public function init(Transport $transportEntity)
    {
        // @TODO Implement init() method.
    }
}
