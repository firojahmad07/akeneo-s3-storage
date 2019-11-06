<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\DependencyInjection\Compiler;

use Ewave\Bundle\AttributeBundle\Property\PropertyConfig;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class CreatePropertyFormExtensionsPass
 *
 * @package Ewave\Bundle\AttributeBundle\DependencyInjection\Compiler
 */
class CreatePropertyFormExtensionsPass implements CompilerPassInterface
{
    public const FORM_EXTENSIONS = 'formExtensions';

    public const FORM_EXTENSION_MODULE = 'module';
    public const FORM_EXTENSION_PARENT = 'parent';
    public const FORM_EXTENSION_PARENTS = 'parents';
    public const FORM_EXTENSION_TARGET_ZONE = 'targetZone';
    public const FORM_EXTENSION_POSITION = 'position';

    public const FORM_EXTENSION_CONFIG = 'config';

    public const FORM_CONFIG_LABEL = 'label';
    public const FORM_CONFIG_REQUIRED = 'required';
    public const FORM_CONFIG_FIELD_NAME = 'fieldName';
    public const FORM_CONFIG_READONLY = 'readOnly';

    /**
     * @var string
     */
    private const SERVICE_ID_EXTENSION_PROVIDER = 'pim_enrich.provider.form_extension';

    private const TAG_PROPERTY_PROVIDER = 'ewave_attribute.provider.attribute_property';

    private $formExtensionCode = 'ewave-attribute-edit-form-property-%s-%03d';
    private const FORM_EXTENSION_TEMPLATE = [
        self::FORM_EXTENSION_MODULE      => null,
        self::FORM_EXTENSION_PARENT      => 'pim-attribute-edit-form-properties-common',
        self::FORM_EXTENSION_POSITION    => 1000,
        self::FORM_EXTENSION_TARGET_ZONE => 'content',
        self::FORM_EXTENSION_CONFIG      => [
            self::FORM_CONFIG_LABEL      => null,
            self::FORM_CONFIG_REQUIRED   => false,
            self::FORM_CONFIG_FIELD_NAME => false,
        ],
    ];

    /**
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        /**
         * @var Definition $configServiceDef
         */
        if (!$container->has(self::SERVICE_ID_EXTENSION_PROVIDER)) {
            throw new \InvalidArgumentException(
                sprintf('Form extension provider service "%s" does not exist', self::SERVICE_ID_EXTENSION_PROVIDER)
            );
        }

        $extensionProviderDef = $container->getDefinition(self::SERVICE_ID_EXTENSION_PROVIDER);
        $configServiceIds = $container->findTaggedServiceIds(self::TAG_PROPERTY_PROVIDER);
        foreach ($configServiceIds as $configServiceId => $tags) {
            $configServiceDef = $container->getDefinition($configServiceId);

            $config = $configServiceDef->getArgument(0);
            if (is_string($config)) {
                $config = $container->getParameter(trim($config, '%'));
            }

            $config = $this->preparePropertyConfig($config);
            $formExtensions = $config[self::FORM_EXTENSIONS] ?? [];
            $configServiceDef->setArgument(0, $config);

            foreach ($formExtensions as $extensionCode => $extension) {
                $extensionProviderDef->addMethodCall('addExtension', [$extensionCode, $extension]);
            }
        }
    }

    /**
     * @param array $propertyConfig
     *
     * @return array
     */
    private function preparePropertyConfig(array $propertyConfig = []): array
    {
        $propertyCode = $propertyConfig[PropertyConfig::PROPERTY_CODE] ?? null;
        $propertyType = $propertyConfig[PropertyConfig::PROPERTY_TYPE] ?? null;
        if (!$propertyCode || !$propertyType) {
            throw new \InvalidArgumentException(
                'Unable to create property config. Property code and type must be filled'
            );
        }

        $propertyConfig[PropertyConfig::REQUIRED] = $propertyConfig[PropertyConfig::REQUIRED] ?? false;

        $formExtensions = [];
        $extensions = $propertyConfig[self::FORM_EXTENSIONS];
        $extensionIndex = 0;
        foreach ($extensions as $extension) {
            $formExtension = array_merge(self::FORM_EXTENSION_TEMPLATE, $extension);

            $module = $formExtension[self::FORM_EXTENSION_MODULE] ?? null;
            $formExtension[self::FORM_EXTENSION_MODULE] =
                $module ?? sprintf('pim/form/common/fields/%s', $propertyType);

            $formExtensionConfig = $formExtension[self::FORM_EXTENSION_CONFIG] ?? [];
            if (!isset($formExtensionConfig[self::FORM_CONFIG_REQUIRED])) {
                $formExtensionConfig[self::FORM_CONFIG_REQUIRED] = $propertyConfig[PropertyConfig::REQUIRED];
            }

            $label = $formExtensionConfig[self::FORM_CONFIG_LABEL] ?? false;
            $formExtensionConfig[self::FORM_CONFIG_LABEL] =
                $label ?? sprintf('ewave_attribute.entity.attribute.property.%s.label', $propertyCode);

            $formExtension[self::FORM_EXTENSION_CONFIG] = $formExtensionConfig;

            $parents = $formExtension[self::FORM_EXTENSION_PARENTS] ?? [];
            if ($parents) {
                $parents = array_filter(array_map('trim', $parents));
                foreach ($parents as $parent) {
                    $formExtension[self::FORM_EXTENSION_PARENT] = $parent;
                    $extensionCode = sprintf($this->formExtensionCode, $propertyCode, $extensionIndex);
                    $formExtensions[$extensionCode] = $formExtension;
                    $extensionIndex++;
                }
            } else {
                $extensionCode = sprintf($this->formExtensionCode, $propertyCode, $extensionIndex);
                $formExtensions[$extensionCode] = $formExtension;
                $extensionIndex++;
            }
        }

        $propertyConfig[self::FORM_EXTENSIONS] = $formExtensions;

        return $propertyConfig;
    }
}
