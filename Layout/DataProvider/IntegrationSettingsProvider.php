<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Layout\DataProvider;

use Dnd\Bundle\DpdFranceShippingBundle\Provider\SettingsProvider;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class IntegrationSettingsProvider
{
    public function __construct(
        private readonly SettingsProvider $settingsProvider
    ) {
    }

    /**
     * Returns the DPD FR integration settings
     */
    public function getSettings(): ParameterBag
    {
        return $this->settingsProvider->getSettings();
    }

    /**
     * Returns google maps api key
     */
    public function getMapsApiKey(): string
    {
        return $this->getSettings()->get('google_maps_api_key') ?? '';
    }
}
