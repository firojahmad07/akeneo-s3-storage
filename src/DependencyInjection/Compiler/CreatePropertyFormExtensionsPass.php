<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Ewave\Bundle\CategoryBundle\Helper\CategoryPropertyHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CreatePropertyFormExtensionsPass
 *
 * @package Ewave\Bundle\CategoryBundle\DependencyInjection\Compiler
 */
class CreatePropertyFormExtensionsPass implements CompilerPassInterface
{
    private const EWAVE_ATTRIBUTE_BUNDLE = "Ewave\Bundle\AttributeBundle\EwaveAttributeBundle";

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $jsonFilePath = $this->getBundleWorkspacePathForJson(self::EWAVE_ATTRIBUTE_BUNDLE, "Resources/public/js");      
        $filesystem         = new Filesystem();       
        $propertyConfigs    = $this->listConfigFiles($container);
        $propertyCollection = [];
       
        foreach ($propertyConfigs as $file) {
            $config = Yaml::parse(file_get_contents($file->getPathName()));
            if (isset($config['parameters']) && is_array($config['parameters'])) {
                $extensionConfig = $config['parameters'];                
                $extensionConfig = $this->getFormattedData($extensionConfig);
                if(empty($extensionConfig)) {
                    continue;
                }

                if(!empty($propertyCollection)) {
                    $propertyCollection = array_merge($propertyCollection, $extensionConfig);
                } else {
                    $propertyCollection = $extensionConfig;
                }
            }
        }
       
        file_put_contents($jsonFilePath, json_encode($propertyCollection));
    }

    /**
     * @param array $extensionConfigData
     * @return array
     */

    public function getFormattedData(array $extensionConfigData)
    {
        $data = [];
        foreach($extensionConfigData as $key => $values) {
            $data[] = $values;
        }

        return $data;
    }

    /**
     * Get all the form configuration files in the Resources/config/ewave/CategoryProperty directories
     *
     * @param ContainerBuilder $container
     *
     * @return \SplFileInfo[]
     */
    protected function listConfigFiles(ContainerBuilder $container)
    {
        $files = [];

        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            $files = array_merge($files, $this->getConfigurationFiles($bundle, 'Resources/config/ewave/AttributeProperty/'));
        }

        return $files;
    }

    /** 
     * Get configuration files for dynamic form
     * 
     * @param string $bundle
     * @param string $path
     * 
     * @return array
     * 
     */
    private function getConfigurationFiles(string $bundle, string $path): array
    {
        $files = [];
        $reflection = new \ReflectionClass($bundle);
        $directory = sprintf(
            '%s/%s/%s',
            dirname($reflection->getFilename()),
            $path,
            'parameters'
        );
        $file = $directory . '.yml';
        if (is_file($file)) {
            $files[] = new \SplFileInfo($file);
        }
        sort($files);

        return $files;
    }
 
    /**
     * Get Bundle workspace path
     * 
     * @param string $bundle
     * @param string $path
     * 
     * @return string
     */   
    private function getBundleWorkspacePathForJson(string $bundle, string $path): string
    {
        $reflection = new \ReflectionClass($bundle);
        return sprintf(
            '%s/%s/%s',
            dirname($reflection->getFilename()),
            $path,
            'attribute_properties.json'
        );
    }
}