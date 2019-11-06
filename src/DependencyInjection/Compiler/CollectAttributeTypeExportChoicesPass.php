<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class CollectAttributeTypeExportChoicesPass
 *
 * @package Ewave\Bundle\AttributeBundle\DependencyInjection\Compiler
 */
class CollectAttributeTypeExportChoicesPass implements CompilerPassInterface
{
    private const EXPORT_PARAMETER_ATTRIBUTE_TYPES = 'ewave_attribute.attribute_export.attribute_types.entity.choices';
    private const ATTRIBUTE_TYPE_REGISTRY_TAG = 'pim_catalog.attribute_type';

    /**
     * Initial attribute registration
     *
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     *
     * @see \Akeneo\Pim\Structure\Bundle\DependencyInjection\Compiler\RegisterAttributeTypePass::process
     *
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServicesIds = $container->findTaggedServiceIds(self::ATTRIBUTE_TYPE_REGISTRY_TAG);

        $attributeTypeChoices = [];
        /** @var Definition $typeService */
        foreach ($taggedServicesIds as $serviceId => $typeTag) {
            if ($alias = $typeTag[0]['alias'] ?? null) {
                $translation = sprintf('pim_enrich.entity.attribute.property.type.%s', $alias);
                $attributeTypeChoices[$alias] = $translation;
            }
        }

        $container->setParameter(self::EXPORT_PARAMETER_ATTRIBUTE_TYPES, $attributeTypeChoices);
    }
}
