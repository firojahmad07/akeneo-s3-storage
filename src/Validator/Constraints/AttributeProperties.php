<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class AttributeProperties
 *
 * @package Ewave\Bundle\AttributeBundle\Validator\Constraints
 */
class AttributeProperties extends Constraint
{
    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'ewave_attribute_properties_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
