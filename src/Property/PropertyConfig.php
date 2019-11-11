<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\Property;

/**
 * Class PropertyConfig
 *
 * @package Ewave\Bundle\AttributeBundle\Provider\Property
 */
class PropertyConfig
{
    public const PROPERTY_CODE = 'propertyCode';
    public const PROPERTY_TYPE = 'propertyType';
    public const ATTRIBUTE_TYPES = 'attributeTypes';
    public const EXPORTABLE = 'exportable';
    public const REQUIRED = 'required';
    public const DEFAULT_VALUE = 'default';
    public const FORM_EXTENSION = 'formExtension';

    /**
     * @var string
     */
    protected $propertyCode;

    /**
     * @var string
     */
    protected $propertyType;

    /**
     * @var array | null
     */
    protected $supportedAttributeTypes;

    /**
     * @var bool
     */
    protected $exportable = false;

    /**
     * @var mixed | null
     */
    protected $defaultValue;

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @var array
     */
    protected $formExtensionConfig = [];

    /**
     * PropertyConfigProvider constructor.
     *
     * @param array $propertyConfig
     */
    public function __construct(array $propertyConfig = [])
    {
        $this->propertyCode = $propertyConfig[self::PROPERTY_CODE] ?? null;
        $this->propertyType = $propertyConfig[self::PROPERTY_TYPE] ?? null;

        if (!$this->propertyCode || !$this->propertyType) {
            throw new \InvalidArgumentException(
                'Unable to create property config. Property code and type must be filled'
            );
        }

        $this->supportedAttributeTypes = $propertyConfig[self::ATTRIBUTE_TYPES] ?? null;
        $this->exportable = $propertyConfig[self::EXPORTABLE] ?? false;
        $this->defaultValue = $propertyConfig[self::DEFAULT_VALUE] ?? null;
        $this->required = $propertyConfig[self::REQUIRED] ?? false;

        $this->formExtensionConfig = $propertyConfig[self::FORM_EXTENSION] ?? [];
    }

    /**
     * @return string
     */
    public function getPropertyCode(): string
    {
        return $this->propertyCode;
    }

    /**
     * @return string
     */
    public function getPropertyType(): string
    {
        return $this->propertyType;
    }

    /**
     * @return array|null
     */
    public function getSupportedAttributeTypes(): ?array
    {
        return $this->supportedAttributeTypes;
    }

    /**
     * @return bool
     */
    public function isExportable(): bool
    {
        return $this->exportable;
    }

    /**
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return array
     */
    public function getFormExtensionConfig(): array
    {
        return $this->formExtensionConfig;
    }
}
