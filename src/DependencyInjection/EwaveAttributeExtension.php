<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class EwaveAttributeExtension
 *
 * @package Ewave\Bundle\AttributeBundle\DependencyInjection
 */
class EwaveAttributeExtension extends Extension
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

        $loader->load('array_converters.yml');
        $loader->load('event_subscribers.yml');
        $loader->load('form_providers.yml');
        $loader->load('job_constraints.yml');
        $loader->load('job_defaults.yml');
        $loader->load('jobs.yml');
        $loader->load('providers.yml');
        $loader->load('query_builders.yml');
        $loader->load('readers.yml');
        $loader->load('steps.yml');
        $loader->load('validators.yml');
    }
}
