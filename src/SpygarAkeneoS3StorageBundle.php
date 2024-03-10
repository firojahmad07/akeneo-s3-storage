<?php
declare(strict_types=1);

namespace Spygar\Bundle\AkeneoS3StorageBundle;

use Ewave\Bundle\AttributeBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AkeneoS3StorageBundle
 *
 * @package Spygar\Bundle\AkeneoS3StorageBundle
 */
class SpygarAkeneoS3StorageBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    // public function build(ContainerBuilder $container)
    // {
    //     $container->addCompilerPass(new Compiler\CollectAttributeTypeExportChoicesPass());
    //     $container->addCompilerPass(new Compiler\CreatePropertyFormExtensionsPass());
    // }
}
