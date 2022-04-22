<?php

namespace JulienIts\EmailsQueueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('julienits_emailsqueue');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = method_exists(TreeBuilder::class, 'getRootNode')
            ? $treeBuilder->getRootNode()
            : $treeBuilder->root('julienits_emailsqueue');

        $rootNode
            ->children()
                ->scalarNode('mode')
                    ->info('If mode is not prod, emails will not be sent to the defined to/cc/bcc but to the debug to, debug cc')
                ->end()
                ->scalarNode('debug_to')->end()
                ->scalarNode('debug_cc')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}