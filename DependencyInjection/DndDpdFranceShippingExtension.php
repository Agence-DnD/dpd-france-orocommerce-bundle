<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DndDpdFranceShippingExtension extends Extension implements PrependExtensionInterface
{
    /**
     * ALIAS constant for the extension
     */
    public const ALIAS = 'dnd_dpd_france_shipping';
    /**
     * Max amount of log files to keep
     */
    public const MAX_ROTATION_LOGFILES = 5;

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator([__DIR__ . '/../Resources/config']));
        $loader->load('commands.yml');
        $loader->load('controllers.yml');
        $loader->load('event_listeners.yml');
        $loader->load('form_types.yml');
        $loader->load('integration.yml');
        $loader->load('mq_topics.yml');
        $loader->load('services.yml');
    }

    /**
     * Adds a monolog channel for the module
     */
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('monolog', [
            'channels' => [self::ALIAS],
            'handlers' => [
                self::ALIAS => [
                    'type' => 'rotating_file',
                    'max_files' => self::MAX_ROTATION_LOGFILES,
                    'path' => "%kernel.logs_dir%/dpd-fr_%kernel.environment%.log",
                    'channels' => [self::ALIAS],
                ],
            ],
        ]);
    }
}
