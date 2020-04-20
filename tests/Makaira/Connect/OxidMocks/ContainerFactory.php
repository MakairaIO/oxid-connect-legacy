<?php

namespace OxidEsales\EshopCommunity\Internal\Container;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ContainerFactory {

    /**
     * @return ContainerFactory
     */
    public static function getInstance()
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new self;
        }

        return $instance;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer() {
        static $container = null;

        if ($container === null) {
            $container = new ContainerBuilder();
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../../../'));
            $loader->load('tests/services.yaml');
            $loader->load('services.yaml');
            $container->compile();
        }

        return $container;
    }
}