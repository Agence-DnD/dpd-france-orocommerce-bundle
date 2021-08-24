<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Integration;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Form\Type\DpdFranceTransportSettingsFormType;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Symfony\Component\HttpFoundation\ParameterBag;

class DpdFranceTransport implements DpdFranceTransportInterface
{
    /**
     * Description $transportEntity field
     *
     * @var DpdFranceTransportSettings $transportEntity
     */
    protected DpdFranceTransportSettings $transportEntity;
    /**
     * Description $settings field
     *
     * @var ParameterBag $settings
     */
    protected ParameterBag $settings;

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'dnd_dpd_france_shipping.integration.transport.label';
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsFormType()
    {
        return DpdFranceTransportSettingsFormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsEntityFQCN()
    {
        return DpdFranceTransportSettings::class;
    }

    /**
     * {@inheritdoc}
     *
     * @return DpdFranceTransportSettings
     */
    public function getTransportEntity(): DpdFranceTransportSettings
    {
        return $this->transportEntity;
    }

    /**
     * {@inheritdoc}
     *
     * @return ParameterBag
     */
    public function getSettings(): ParameterBag
    {
        return $this->settings;
    }

    /**
     * Description init function
     *
     * @param Transport $transportEntity
     *
     * @return void
     */
    public function init(Transport $transportEntity)
    {
        $this->transportEntity = $transportEntity;
        $this->settings        = $this->transportEntity->getSettingsBag();
    }
}
