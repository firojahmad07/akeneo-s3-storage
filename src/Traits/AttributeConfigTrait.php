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
        $propertyConfiguration  = $this->getPropertyConfiguration();
        $customProperties       = [];
        if (!empty($propertyConfiguration)) {
            foreach($propertyConfiguration as $configuration) {
                if(in_array($configuration['config']['fieldName'], $this->properties)) {
                    continue;
                }
                $this->properties[] = $configuration['config']['fieldName'];
            }
        }
    }

    /**
     * Get Property configuration
     */
    public function getPropertyConfiguration()
    {
        return $this->getAttributeProperties(self::EWAVE_ATTRIBUTE_BUNDLE, self::EWAVE_ATTRIBUTE_BUNDLE_RESOURCE);
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
        $fileContents = [];
        if (file_exists($filePath)) {
            $fileContents = json_decode(file_get_contents($filePath), true);
        }
        $formattedContent = $this->getFormattedContent($fileContents);

        return $formattedContent;
   }

   /**
    * Get formatted contents
    * @param array $fileContent
    * @return array $fileContent
    */
    public function getFormattedContent(array $fileContents) 
    {
        $formattedData = [];
        foreach($fileContents as $fileContent) {
            $formattedData[$fileContent['config']['fieldName']] = $fileContent;
        }

        return $formattedData;
    }
}