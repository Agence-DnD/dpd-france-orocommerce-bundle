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

    public function getData(): ParameterBag
    {
        $settings = $this->settingsProvider->getSettings();

        dump($settings);

        return $settings;
    }
}
