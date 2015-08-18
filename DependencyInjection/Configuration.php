<?php

namespace Pm\DocumentationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pm_documentation');

        $rootNode
            ->children()
                ->scalarNode('doc_path')
                    ->defaultValue('doc')
                    ->info('documentation (markdown) path relative to app_root')
                ->end()
                ->scalarNode('image_path')
                    ->defaultValue('doc/images')
                    ->info('image path relative to app_root')
                ->end()
                ->scalarNode('view')
                    ->defaultValue('AppBundle::layout.html.twig')
                    ->info('template to render html-pages (needs "content"-block)')
                ->end()
            ->end();


        return $treeBuilder;
    }
}
