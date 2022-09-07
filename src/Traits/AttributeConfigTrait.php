<?php
namespace Ewave\Bundle\AttributeBundle\Traits;

/**
 * Attribute property configuration trait
 */
trait AttributeConfigTrait
{
    /**
     * set custom properties
     */
    public function addCustomFieldsInProperties()
    {
        $propertyConfiguration  = $this->getAttributeProperties(self::EWAVE_ATTRIBUTE_BUNDLE, self::EWAVE_ATTRIBUTE_BUNDLE_RESOURCE);
        $customProperties       = [];
        if (!empty($propertyConfiguration)) {
            foreach($propertyConfiguration as $configuration) {
                array_push($this->properties, $configuration['config']['fieldName']);
            }
        }
    }

    /**
      * Get Bundle attribute configuration file path 
      * @param string $bundle
      * @param string $path
      * 
      * @return array
      */
    protected function getAttributeProperties(string $bundle, string $path)
    {
        $reflection = new \ReflectionClass($bundle);
        $filePath = sprintf(
            '%s/%s/%s',
            dirname($reflection->getFilename()),
            $path,
            'attribute_properties.json'
        );
        $fileContent = [];
        if (file_exists($filePath)) {
            $fileContent = json_decode(file_get_contents($filePath), true);
        }
        
        return $fileContent;
   }
}