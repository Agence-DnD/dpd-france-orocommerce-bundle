<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class DndDpdFranceShippingExtension
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\DependencyInjection
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DndDpdFranceShippingExtension extends Extension implements PrependExtensionInterface
{
    /**
     * ALIAS constant for the extension
     *
     * @var string ALIAS
     */
    public const ALIAS = 'dnd_dpd_france_shipping';

    /**
     * {@inheritDoc}
     *
     * @param mixed[]          $configs
     * @param ContainerBuilder $container
     *
     * @return void
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var YamlFileLoader $loader */
        $loader = new YamlFileLoader($container, new FileLocator([__DIR__ . '/../Resources/config']));
        $loader->load('controllers.yml');
        $loader->load('event_listeners.yml');
        $loader->load('form_types.yml');
        $loader->load('integration.yml');
        $loader->load('services.yml');
    }

    /**
     * Adds a monolog channel for the module
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('monolog', [
            'channels' => [self::ALIAS],
            'handlers' => [
                self::ALIAS => [
                    'type' => 'rotating_file',
                    'max_files' => 5,
                    'path' => "%kernel.logs_dir%/dpd-fr_%kernel.environment%.log",
                    'channels' => [ self::ALIAS ],
                ],
            ],
        ]);
    }
}
