<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle;

use Ewave\Bundle\AttributeBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class EwaveAttributeBundle
 *
 * @package Ewave\Bundle\AttributeBundle
 */
class EwaveAttributeBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new Compiler\CollectAttributeTypeExportChoicesPass());
        $container->addCompilerPass(new Compiler\CreatePropertyFormExtensionsPass());
    }
}
