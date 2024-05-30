<?php
declare(strict_types=1);

namespace Spygar\Bundle\AkeneoS3StorageBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class SpygarAkeneoS3StorageExtension
 *
 * @package Spygar\Bundle\AkeneoS3StorageBundle\DependencyInjection
 */
class SpygarAkeneoS3StorageExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('controllers.yml');
        $loader->load('file_storage.yml');
        $loader->load('commands.yml');        
    }
}
