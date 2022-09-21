<?php

namespace Ewave\Bundle\AttributeBundle\Updater;

use Akeneo\Channel\Component\Repository\LocaleRepositoryInterface;
use Akeneo\Pim\Structure\Component\Updater\AttributeUpdater as BaseAttributeUpdater;
use Akeneo\Pim\Structure\Component\AttributeTypeRegistry;
use Akeneo\Pim\Structure\Component\Model\AttributeGroupInterface;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Pim\Structure\Component\Repository\AttributeGroupRepositoryInterface;
use Akeneo\Tool\Component\Localization\TranslatableUpdater;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidObjectException;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyException;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Akeneo\Tool\Component\StorageUtils\Exception\UnknownPropertyException;
use Akeneo\Tool\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Ewave\Bundle\AttributeBundle\Traits\AttributeConfigTrait;

/**
 * Updates an attribute.
 */
class AttributeUpdater extends BaseAttributeUpdater
{
    use AttributeConfigTrait;
    
    private const EWAVE_ATTRIBUTE_BUNDLE = "Ewave\Bundle\AttributeBundle\EwaveAttributeBundle";
    private const EWAVE_ATTRIBUTE_BUNDLE_RESOURCE = "Resources/public/js";
 
    /** @var array */
    private $properties;

    /**
     * @param AttributeGroupRepositoryInterface $attrGroupRepo
    * @param LocaleRepositoryInterface         $localeRepository
    * @param AttributeTypeRegistry             $registry
    * @param TranslatableUpdater               $translatableUpdater
    * @param array                             $properties
    */
    public function __construct(
        AttributeGroupRepositoryInterface $attrGroupRepo,
        LocaleRepositoryInterface $localeRepository,
        AttributeTypeRegistry $registry,
        TranslatableUpdater $translatableUpdater,
        array $properties
    ) {
        parent::__construct($attrGroupRepo, $localeRepository, $registry, $translatableUpdater, $properties);
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function update($attribute, array $data, array $options = [])
    {
        $this->addCustomFieldsInProperties();
        if (!$attribute instanceof AttributeInterface) {
            throw InvalidObjectException::objectExpected(
                ClassUtils::getClass($attribute),
                AttributeInterface::class
            );
        }
        $formattedData = $this->getFormattedData($data);
        foreach ($formattedData as $field => $value) {
            $this->validateDataType($field, $value);
            $this->setData($attribute, $field, $value);
        }

        return $this;
    }

    /**
     * Get formatted data
     * 
     * @param array $formattedData
     * @return array
     */
    public function getFormattedData(array $data)
    {
        foreach($data as $field => $value) {
            if(!in_array($field, $this->properties)) {
                continue;
            }
            
            $data[$field] = is_array($value) ? json_encode($value) : $value;
        }

        return $data;
    }

    /**
     * Validate the data type of a field.
     *
     * @param string $field
     * @param mixed  $data
     *
     * @throws InvalidPropertyTypeException
     * @throws UnknownPropertyException
     */
    protected function validateDataType($field, $data)
    {
        if (in_array($field, ['labels', 'available_locales', 'allowed_extensions', 'guidelines'])) {
            if (!is_array($data)) {
                throw InvalidPropertyTypeException::arrayExpected($field, static::class, $data);
            }

            foreach ($this->filterReadOnlyFields($data) as $key => $value) {
                if (null !== $value && !is_scalar($value)) {
                    throw InvalidPropertyTypeException::validArrayStructureExpected(
                        $field,
                        sprintf('one of the "%s" values is not a scalar', $field),
                        static::class,
                        $data
                    );
                }
            }
        } elseif (in_array(
            $field,
            array_merge([
                'code',
                'type',
                'group',
                'unique',
                'useable_as_grid_filter',
                'metric_family',
                'default_metric_unit',
                'reference_data_name',
                'max_characters',
                'validation_rule',
                'validation_regexp',
                'wysiwyg_enabled',
                'number_min',
                'number_max',
                'decimals_allowed',
                'negative_allowed',
                'date_min',
                'date_max',
                'max_file_size',
                'minimum_input_length',
                'sort_order',
                'localizable',
                'scopable',
                'required',
            ], $this->properties)
        )) {
            if (null !== $data && !is_scalar($data)) {
                throw InvalidPropertyTypeException::scalarExpected($field, static::class, $data);
            }
        } elseif ('table_configuration' === $field) {
            if (!is_array($data)) {
                throw InvalidPropertyTypeException::arrayExpected($field, static::class, $data);
            }
        } else {
            throw UnknownPropertyException::unknownProperty($field);
        }
    }

    /**
     * @param AttributeInterface $attribute
     * @param string             $field
     * @param mixed              $data
     *
     * @throws InvalidPropertyException
     * @throws UnknownPropertyException
     */
    protected function setData(AttributeInterface $attribute, $field, $data)
    {
        switch ($field) {
            case 'type':
                $this->setType($attribute, $data);
                break;
            case 'labels':
                $this->translatableUpdater->update($attribute, $data);
                break;
            case 'group':
                $this->setGroup($attribute, $data);
                break;
            case 'available_locales':
                $this->setAvailableLocales($attribute, $field, $data);
                break;
            case 'date_min':
                $this->validateDateFormat('date_min', $data);
                $date = $this->getDate($data);
                $attribute->setDateMin($date);
                break;
            case 'date_max':
                $this->validateDateFormat('date_max', $data);
                $date = $this->getDate($data);
                $attribute->setDateMax($date);
                break;
            case 'allowed_extensions':
                $attribute->setAllowedExtensions(implode(',', $data));
                break;
            case 'guidelines':
                foreach ($data as $localeCode => $localeGuidelines) {
                    if (null === $localeGuidelines || '' === $localeGuidelines) {
                        $attribute->removeGuidelines($localeCode);
                    } else {
                        $attribute->addGuidelines($localeCode, $localeGuidelines);
                    }
                }
                break;
            case 'table_configuration':
                $attribute->setRawTableConfiguration($data);
                break;
            default:
                if (in_array($field, $this->properties)) {
                    $attribute->setProperty($field, $data);
                } else {
                    $this->setValue($attribute, $field, $data);
                }
        }
    }

    
    /**
     * @param $dataToFilter
     * 
     * @return array
     */
    private function filterReadOnlyFields(array $dataToFilter) : array
    {
        $readOnlyFields = ['group_labels'];

        return array_filter($dataToFilter, function ($key) use ($readOnlyFields) {
            return !in_array($key, $readOnlyFields);
        }, ARRAY_FILTER_USE_KEY);
        parent::validateDataType($field, $data);
    }
}
