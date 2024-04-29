<?php

namespace Willydamtchou\SymfonyMapstruct\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class WillydamtchouSymfonyMapstructExtension extends Extension
{
    public const string CONFIG_DIRECTORY = '/../../config';
    public const string SERVICES_FILES = 'services.yaml';

    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . (self::CONFIG_DIRECTORY))
        );

        $loader->load(self::SERVICES_FILES);
    }
}
