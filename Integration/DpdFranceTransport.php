<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Integration;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Form\Type\DpdFranceTransportSettingsFormType;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class DpdFranceTransport
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Integration
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
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
     * @var ParameterBag|null $settings
     */
    protected ?ParameterBag $settings;

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'dnd_dpd_france_shipping.integration.transport.label';
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsFormType(): string
    {
        return DpdFranceTransportSettingsFormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsEntityFQCN(): string
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
    public function init(Transport $transportEntity): void
    {
        $this->transportEntity = $transportEntity;
        $this->settings        = $this->transportEntity->getSettingsBag();
    }
}
