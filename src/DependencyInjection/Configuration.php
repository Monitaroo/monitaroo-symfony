<?php

declare(strict_types=1);

namespace Monitaroo\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration for MonitarooBundle.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        // Symfony 2.8/3.x compatibility
        if (method_exists(TreeBuilder::class, 'root')) {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('monitaroo');
        } else {
            $treeBuilder = new TreeBuilder('monitaroo');
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->children()
                ->scalarNode('api_key')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info('Your Monitaroo API key (required)')
                ->end()
                ->scalarNode('endpoint')
                    ->defaultValue('https://api.monitaroo.com')
                    ->info('Monitaroo API endpoint')
                ->end()
                ->scalarNode('service')
                    ->defaultNull()
                    ->info('Service name (defaults to kernel.project_dir basename)')
                ->end()
                ->scalarNode('environment')
                    ->defaultNull()
                    ->info('Environment (defaults to kernel.environment)')
                ->end()
                ->scalarNode('host')
                    ->defaultNull()
                    ->info('Host name (auto-detected if not set)')
                ->end()
                ->integerNode('batch_size')
                    ->defaultValue(100)
                    ->min(1)
                    ->max(1000)
                    ->info('Number of items to buffer before auto-flushing')
                ->end()
                ->booleanNode('auto_flush')
                    ->defaultTrue()
                    ->info('Automatically flush on kernel.terminate')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
