<?php

namespace Ewave\Bundle\AttributeBundle\Normalizer\Standard;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Akeneo\Pim\Structure\Component\Normalizer\Standard\AttributeNormalizer as BaseAttributeNormalizer;
use Ewave\Bundle\AttributeBundle\Traits\AttributeConfigTrait;

/**
 * A normalizer to transform an AttributeInterface entity into array
 */
class AttributeNormalizer extends BaseAttributeNormalizer
{
    use AttributeConfigTrait;
    private const EWAVE_ATTRIBUTE_BUNDLE = "Ewave\Bundle\AttributeBundle\EwaveAttributeBundle";
    private const EWAVE_ATTRIBUTE_BUNDLE_RESOURCE = "Resources/public/js";

    /** @var array */
    private $properties;

    /**
     * @param NormalizerInterface $translationNormalizer
     * @param NormalizerInterface $dateTimeNormalizer
     * @param array               $properties
     */
    public function __construct(
        NormalizerInterface $translationNormalizer,
        NormalizerInterface $dateTimeNormalizer,
        array $properties
    ) {
        parent::__construct($translationNormalizer, $dateTimeNormalizer, $properties);
        $this->properties = $properties;
    }
    
     /**
     * {@inheritdoc}
     *
     * @param AttributeInterface $attribute
     */
    public function normalize($attribute, $format = null, array $context = [])
    {
        $this->addCustomFieldsInProperties();
        
        return parent::normalize($attribute, $format, $context);
    }
}
