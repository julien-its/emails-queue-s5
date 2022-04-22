<?php

namespace JulienIts\EmailsQueueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('acme_social');

        $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('twitter')
            ->children()
            ->integerNode('client_id')->end()
            ->scalarNode('client_secret')->end()
            ->end()
            ->end() // twitter
            ->end()
        ;

        return $treeBuilder;

        /*$treeBuilder = new TreeBuilder('julien_its_emails_queue');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('mode')->end()
            ->end()
        ;

        return $treeBuilder;*/
    }
}