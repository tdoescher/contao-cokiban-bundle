<?php

/**
 * This file is part of Cokiban Bundle for Contao
 *
 * @package     tdoescher/cokiban-bundle
 * @author      Torben Döscher <mail@tdoescher.de>
 * @license     LGPL
 * @copyright   tdoescher.de - WEB und IT <https://tdoescher.de>
 */

namespace tdoescher\CokibanBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use tdoescher\CokibanBundle\DependencyInjection\Configuration;

class CokibanExtension extends Extension
{ 
    public function load(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator( __DIR__ . '/../Resources/config'));
        $loader->load('listener.yaml');

        $config = $this->processConfiguration(new Configuration(), $mergedConfig);
        $container->setParameter('cokiban', $config);
    }
}
