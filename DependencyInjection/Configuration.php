<?php

namespace Rj\EmailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('rj_email');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->scalarNode('default_locale')->defaultValue('en')->end()
            ->scalarNode('default_from_name')->defaultValue('ACME Corporation')->end()
            ->scalarNode('default_from_email')->defaultValue('acme@example.com')->end()
            ->arrayNode('locales')
                ->isRequired()
                ->prototype('scalar')->end()
            ;

        $this->addEmailsSection($rootNode);
        return $treeBuilder;
    }

    private function addEmailsSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('emails')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('confirmation')->defaultValue('confirmation')->end()
                        ->scalarNode('resetting')->defaultValue('resetting')->end()
                    ->end()
                ->end()
            ->end();
    }
}
