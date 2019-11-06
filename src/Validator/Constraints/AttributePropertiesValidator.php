<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\Validator\Constraints;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Ewave\Bundle\AttributeBundle\Helper\PropertyHelper;
use Ewave\Bundle\AttributeBundle\Property\PropertyConfig;
use Ewave\Bundle\AttributeBundle\Property\Provider\PropertyConfigProvider;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AttributePropertiesValidator
 *
 * @package Ewave\Bundle\AttributeBundle\Validator\Constraints
 */
class AttributePropertiesValidator extends ConstraintValidator
{
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var PropertyConfigProvider
     */
    private $propertyConfigProvider;

    public function __construct(PropertyConfigProvider $propertyConfigProvider, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->propertyConfigProvider = $propertyConfigProvider;
    }

    public function validate($attribute, Constraint $constraint)
    {
        if (!$attribute instanceof AttributeInterface) {
            return;
        }

        $propertyConfigs = $this->propertyConfigProvider->getAttributePropertyConfigs($attribute);

        foreach ($propertyConfigs as $propertyCode => $propertyConfig) {
            $propertyValue = $attribute->getProperty($propertyCode);
            $this->validateByConfig($propertyValue, $propertyConfig);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateByConfig($propertyValue, PropertyConfig $propertyConfig)
    {
        $constraints = $this->getPropertyTypeConstraints($propertyConfig);

        $internalViolations = $this->validator->validate($propertyValue, $constraints);
        if ($internalViolations->count()) {
            foreach ($internalViolations as $internalViolation) {
                $this->context
                    ->buildViolation($internalViolation->getMessage())
                    ->atPath($propertyConfig->getPropertyCode())
                    ->addViolation();
            }
        }

        return $this;
    }

    /**
     * @param PropertyConfig $propertyConfig
     *
     * @return array
     */
    private function getPropertyTypeConstraints(PropertyConfig $propertyConfig)
    {
        $constraints = [];
        switch ($propertyConfig->getPropertyType()) {
            case PropertyHelper::TYPE_BOOLEAN:
                $constraints[] = new Constraints\Type('bool');
                if ($propertyConfig->isRequired()) {
                    $constraints[] = new Constraints\NotNull();
                }
                break;
            case PropertyHelper::TYPE_INTEGER:
                $constraints[] = new Constraints\Type('integer');
                if ($propertyConfig->isRequired()) {
                    $constraints[] = new Constraints\NotNull();
                }
                break;
            case PropertyHelper::TYPE_FLOAT:
                $constraints[] = new Constraints\Type('float');
                if ($propertyConfig->isRequired()) {
                    $constraints[] = new Constraints\NotNull();
                }
                break;
            case PropertyHelper::TYPE_TEXT:
                $constraints[] = new Constraints\Type('string');
                if ($propertyConfig->isRequired()) {
                    $constraints[] = new Constraints\NotBlank();
                }
                break;
            default:
        }

        return $constraints;
    }
}
