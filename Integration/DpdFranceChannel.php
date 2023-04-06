<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Integration;

use Oro\Bundle\IntegrationBundle\Provider\{
    ChannelInterface,
    IconAwareIntegrationInterface};

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceChannel implements ChannelInterface, IconAwareIntegrationInterface
{
    /**
     * Name of channel
     */
    public const TYPE = 'dpd_france_shipping';

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'dnd_dpd_france_shipping.integration.channel.label';
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon(): string
    {
        return 'bundles/dnddpdfranceshipping/img/DPD_logo_icon.png';
    }
}
