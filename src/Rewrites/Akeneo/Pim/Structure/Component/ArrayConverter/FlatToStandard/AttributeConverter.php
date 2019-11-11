<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\Rewrites\Akeneo\Pim\Structure\Component\ArrayConverter\FlatToStandard;

use Akeneo\Pim\Structure\Component\ArrayConverter\FlatToStandard\Attribute as BaseAttributeConverter;
use Akeneo\Tool\Component\Connector\ArrayConverter\FieldsRequirementChecker;
use Ewave\Bundle\AttributeBundle\Helper\PropertyHelper;
use Ewave\Bundle\AttributeBundle\Property\Provider\PropertyConfigProvider;

/**
 * Class AttributeConverter
 *
 * @package Ewave\Bundle\AttributeBundle\Rewrites\Akeneo\Pim\Structure\Component\ArrayConverter\FlatToStandard
 */
class AttributeConverter extends BaseAttributeConverter
{
    /**
     * @var PropertyConfigProvider
     */
    private $propertyConfigProvider;

    /**
     * @param FieldsRequirementChecker $fieldChecker
     * @param array                    $booleanFields
     * @param PropertyConfigProvider   $propertyConfigProvider
     */
    public function __construct(
        FieldsRequirementChecker $fieldChecker,
        array $booleanFields,
        PropertyConfigProvider $propertyConfigProvider
    ) {
        parent::__construct($fieldChecker, $booleanFields);

        $this->propertyConfigProvider = $propertyConfigProvider;
    }

    /**
     * @param string $field
     * @param array  $booleanFields
     * @param array  $data
     * @param array  $convertedItem
     *
     * @return array
     */
    protected function convertFields($field, $booleanFields, $data, $convertedItem)
    {
        $propertyConfig = $this->propertyConfigProvider->getPropertyConfig($field);

        if (!$propertyConfig) {
            return parent::convertFields($field, $booleanFields, $data, $convertedItem);
        }

        if (null === $data) {
            $convertedItem[$field] = null;

            return $convertedItem;
        }

        switch ($propertyConfig->getPropertyType()) {
            case PropertyHelper::TYPE_BOOLEAN:
                $convertedItem[$field] = $this->convertBoolean($data);
                break;
            case PropertyHelper::TYPE_INTEGER:
                $convertedItem[$field] = $this->convertInteger($data);
                break;
            case PropertyHelper::TYPE_FLOAT:
                $convertedItem[$field] = $this->convertFloat($data);
                break;
            case PropertyHelper::TYPE_TEXT:
                $convertedItem[$field] = $this->convertString($data);
                break;
            default:
                return parent::convertFields($field, $booleanFields, $data, $convertedItem);
        }

        return $convertedItem;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function convertBoolean($value)
    {
        return '1' === $value || '0' === $value ? (bool)$value : $value;
    }

    /**
     * @param $value
     *
     * @return int
     */
    protected function convertInteger($value)
    {
        return (int)$value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function convertString($value)
    {
        return (string)$value;
    }
}
