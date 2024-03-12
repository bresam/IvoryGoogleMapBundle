<?php

/*
 * This file is part of the Ivory Google Map bundle package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = $this->createTreeBuilder('ivory_google_map');
        $children = $treeBuilder->getRootNode()
            ->children()
            ->append($this->createMapNode())
            ->append($this->createStaticMapNode());

        $services = [
            'direction'          => true,
            'distance_matrix'    => true,
            'elevation'          => true,
            'geocoder'           => true,
            'place_autocomplete' => true,
            'place_detail'       => true,
            'place_photo'        => false,
            'place_search'       => true,
            'time_zone'          => true,
        ];

        foreach ($services as $service => $http) {
            $children->append($this->createServiceNode($service, $http));
        }

        return $treeBuilder;
    }

    private function createMapNode(): ArrayNodeDefinition
    {
        return $this->createNode('map')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('debug')->defaultValue('%kernel.debug%')->end()
            ->scalarNode('language')->defaultValue('%locale%')->end()
            ->scalarNode('api_key')->end()
            ->end();
    }

    private function createStaticMapNode(): ArrayNodeDefinition
    {
        return $this->createNode('static_map')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('api_key')->end()
            ->append($this->createBusinessAccountNode(false))
            ->end();
    }

    private function createServiceNode(string $service, bool $http): ArrayNodeDefinition
    {
        $node = $this->createNode($service);
        $children = $node
            ->children()
            ->scalarNode('api_key')->end()
            ->append($this->createBusinessAccountNode(true));

        if ($http) {
            $children
                ->scalarNode('client')
                ->isRequired()
                ->cannotBeEmpty()
                ->end()
                ->scalarNode('message_factory')
                ->isRequired()
                ->cannotBeEmpty()
                ->end()
                ->scalarNode('format')->end();
        } else {
            $node
                ->beforeNormalization()
                ->ifNull()
                ->then(function () {
                    return [];
                })
                ->end();
        }

        return $node;
    }

    private function createBusinessAccountNode(bool $service): ArrayNodeDefinition
    {
        $node = $this->createNode('business_account');
        $clientIdNode = $node->children()
            ->scalarNode('secret')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('channel')->end()
            ->scalarNode('client_id');

        if ($service) {
            $clientIdNode
                ->isRequired()
                ->cannotBeEmpty();
        }

        return $node;
    }

    private function createNode(string $name = null, string $type = 'array'): ArrayNodeDefinition|NodeDefinition
    {
        return $this->createTreeBuilder($name, $type)->getRootNode();
    }

    private function createTreeBuilder(string $name = null, string $type = 'array'): TreeBuilder
    {
        return new TreeBuilder($name, $type);
    }
}
