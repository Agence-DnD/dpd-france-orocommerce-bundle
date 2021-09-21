<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Layout\DataProvider;

use Dnd\Bundle\DpdFranceShippingBundle\Provider\SettingsProvider;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class IntegrationSettingsProvider
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Layout\Dataprovider
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class IntegrationSettingsProvider
{
    /**
     * Description $settingsProvider field
     *
     * @var SettingsProvider $settingsProvider
     */
    protected SettingsProvider $settingsProvider;

    /**
     * IntegrationSettingsProvider constructor
     *
     * @param SettingsProvider $settingsProvider
     */
    public function __construct(SettingsProvider $settingsProvider)
    {
        $this->settingsProvider = $settingsProvider;
    }

    /**
     * Returns the DPD FR integration settings
     *
     * @return ParameterBag
     */
    public function getSettings(): ParameterBag
    {
        return $this->settingsProvider->getSettings();
    }

    /**
     * Returns google maps api key
     *
     * @return mixed
     */
    public function getMapsApiKey(): string
    {
        return $this->getSettings()->get('google_maps_api_key') ?? '';
    }
}
