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
        $this->addCustomFieldsInProperties();
        parent::validateDataType($field, $data);
    }
}
