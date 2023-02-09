<?php

/**
 * This file is part of CokibanBundle for Contao
 *
 * @package     tdoescher/cokiban-bundle
 * @author      Torben Döscher <mail@tdoescher.de>
 * @license     LGPL
 * @copyright   tdoescher.de // WEB & IT <https://tdoescher.de>
 */

namespace tdoescher\CokibanBundle\DependencyInjection;

use Contao\Controller;
use Contao\System;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
  public function getConfigTreeBuilder(): TreeBuilder
  {
    $treeBuilder = new TreeBuilder('cokiban');

    $treeBuilder
      ->getRootNode()
      ->children()
        ->arrayNode('banners')
          ->arrayPrototype()
            ->children()
              ->integerNode('version')->end()
              ->integerNode('days')->end()
              ->arrayNode('groups')
                ->arrayPrototype()
                  ->arrayPrototype()
                    ->scalarPrototype()->end()
                  ->end()
                ->end()
              ->end()
              ->arrayNode('pages')
                ->scalarPrototype()->end()
              ->end()
              ->scalarNode('template')->end()
            ->end()
          ->end()
        ->end()
        ->arrayNode('translations')
          ->arrayPrototype()
            ->children()
              ->arrayNode('banner')
                ->children()
                  ->scalarNode('main_headline')->end()
                  ->scalarNode('main_text')->end()
                  ->scalarNode('save_button_text')->end()
                  ->scalarNode('save_button_title')->end()
                  ->scalarNode('accept_button_text')->end()
                  ->scalarNode('accept_button_title')->end()
                  ->scalarNode('details_headline')->end()
                  ->scalarNode('details_link_text')->end()
                  ->scalarNode('details_link_title')->end()
                  ->scalarNode('details_back_link_text')->end()
                  ->scalarNode('details_back_link_title')->end()
                  ->scalarNode('footer_text')->end()
                  ->scalarNode('switch_true')->end()
                  ->scalarNode('switch_false')->end()
                ->end()
              ->end()
              ->arrayNode('button')
                ->children()
                  ->scalarNode('title')->end()
                  ->scalarNode('text')->end()
                  ->scalarNode('class')->end()
                ->end()
              ->end()
              ->arrayNode('groups')
                ->arrayPrototype()
                  ->children()
                    ->scalarNode('headline')->end()
                    ->scalarNode('text')->end()
                  ->end()
                ->end()
              ->end()
              ->arrayNode('cookies')
                ->arrayPrototype()
                  ->children()
                    ->scalarNode('headline')->end()
                    ->scalarNode('text')->end()
                  ->end()
                ->end()
              ->end()
              ->arrayNode('replacements')
                ->arrayPrototype()
                  ->children()
                    ->scalarNode('button')->end()
                    ->scalarNode('text')->end()
                    ->scalarNode('background')->end()
                    ->scalarNode('template')->end()
                  ->end()
                ->end()
              ->end()
            ->end()
          ->end()
        ->end()
      ->end();

    return $treeBuilder;
  }
}
