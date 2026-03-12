<?php

declare(strict_types=1);

namespace Monitaroo\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * MonitarooExtension loads and manages the bundle configuration.
 */
class MonitarooExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set parameters
        $container->setParameter('monitaroo.api_key', $config['api_key']);
        $container->setParameter('monitaroo.endpoint', $config['endpoint']);
        $container->setParameter('monitaroo.service', $config['service']);
        $container->setParameter('monitaroo.environment', $config['environment']);
        $container->setParameter('monitaroo.host', $config['host']);
        $container->setParameter('monitaroo.batch_size', $config['batch_size']);
        $container->setParameter('monitaroo.auto_flush', $config['auto_flush']);

        // Load services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.xml');
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return 'monitaroo';
    }
}
