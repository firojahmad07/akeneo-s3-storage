<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\Property\Provider;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Ewave\Bundle\AttributeBundle\Property\PropertyConfig;

/**
 * Class PropertyConfigProvider
 *
 * @package Ewave\Bundle\AttributeBundle\Property\Provider
 */
class PropertyConfigProvider
{
    /**
     * @var PropertyConfig[]|iterable
     */
    protected $propertyConfigs;

    /**
     * Property configs indexed by property code
     *
     * @var array
     */
    protected $indexedPropertyConfigs;

    /**
     * PropertyConfigProvider constructor.
     *
     * @param iterable | PropertyConfig[] $propertyConfigs
     */
    public function __construct(iterable $propertyConfigs)
    {
        $this->propertyConfigs = $propertyConfigs;
    }

    /**
     * @param AttributeInterface $attribute
     *
     * @return string[]
     */
    public function getAttributePropertyCodes(AttributeInterface $attribute): array
    {
        return array_keys($this->getAttributePropertyConfigs($attribute));
    }

    /**
     * @param AttributeInterface $attribute
     *
     * @return PropertyConfig[]
     */
    public function getAttributePropertyConfigs(AttributeInterface $attribute): array
    {
        $propertyConfigs = [];
        foreach ($this->getAllPropertyConfigs() as $code => $config) {
            $attributeTypes = $config->getSupportedAttributeTypes();
            if (null === $attributeTypes || in_array($attribute->getType(), $attributeTypes)) {
                $propertyConfigs[$code] = $config;
            }
        }

        return $propertyConfigs;
    }

    /**
     * @return PropertyConfig[]
     */
    public function getAllPropertyConfigs(): array
    {
        if (null === $this->indexedPropertyConfigs) {
            foreach ($this->propertyConfigs as $config) {
                $this->indexedPropertyConfigs[$config->getPropertyCode()] = $config;
            }
        }

        return $this->indexedPropertyConfigs;
    }

    /**
     * @param AttributeInterface $attribute
     *
     * @return string[]
     */
    public function getExportableAttributePropertyCodes(AttributeInterface $attribute): array
    {
        return array_keys($this->getExportableAttributePropertyConfigs($attribute));
    }

    /**
     * @param AttributeInterface $attribute
     *
     * @return PropertyConfig[]
     */
    public function getExportableAttributePropertyConfigs(AttributeInterface $attribute): array
    {
        $propertyConfigs = [];
        foreach ($this->getAttributePropertyConfigs($attribute) as $code => $config) {
            if ($config->isExportable()) {
                $propertyConfigs[$code] = $config;
            }
        }

        return $propertyConfigs;
    }

    /**
     * @param string $code
     *
     * @return PropertyConfig | null
     */
    public function getPropertyConfig(string $code): ?PropertyConfig
    {
        return $this->getAllPropertyConfigs()[$code] ?? null;
    }
}
