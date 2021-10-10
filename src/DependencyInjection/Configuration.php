<?php

namespace Darkweak\SouinApiPlatformBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = (new TreeBuilder('souin_api_platform'));

        $treeBuilder
            ->getRootNode()
            ->children()
                ->arrayNode('api')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('authentication')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('password')
                                    ->defaultValue('')
                                ->end()
                                ->scalarNode('path')
                                    ->defaultValue('/authentication')
                                ->end()
                                ->scalarNode('username')
                                    ->defaultValue('')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('souin')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('path')
                                    ->defaultValue('/souin')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('base')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('host')
                            ->defaultValue('http://caddy')
                        ->end()
                        ->scalarNode('api_path')
                            ->defaultValue('/souin-api')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
