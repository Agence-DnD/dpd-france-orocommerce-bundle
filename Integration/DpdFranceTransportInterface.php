<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Integration;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

interface DpdFranceTransportInterface extends TransportInterface
{
    /**
     * Description getTransportEntity function
     *
     * @return DpdFranceTransportSettings
     */
    public function getTransportEntity(): DpdFranceTransportSettings;

    /**
     * Description getSettings function
     *
     * @return ParameterBag
     */
    public function getSettings(): ParameterBag;

    /**
     * Description getFile function
     *
     * @TODO implement that later, when the library for the SFTP protocol
     *
     * @return string
     */
    //public function getFile(): string;
}
