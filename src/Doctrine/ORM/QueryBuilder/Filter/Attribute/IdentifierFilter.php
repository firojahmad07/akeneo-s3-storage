<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\Doctrine\ORM\QueryBuilder\Filter\Attribute;

use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Doctrine\ORM\QueryBuilder;
use Ewave\Bundle\CoreBundle\Doctrine\ORM\QueryBuilder\Filter\AbstractFilter;

/**
 * Class IdentifierFilter
 *
 * @package Ewave\Bundle\AttributeBundle\Doctrine\ORM\QueryBuilder\Filter\Attribute
 */
class IdentifierFilter extends AbstractFilter
{
    /**
     * @param QueryBuilder $qb
     * @param string       $field
     * @param string       $operator
     * @param mixed        $value
     * @param array        $context
     *
     * @return mixed
     * @throws \Exception
     */
    public function applyFilter(QueryBuilder $qb, $field, $operator, $value, array $context = [])
    {
        $this->checkValue($operator, $field, $value);

        $alias = $this->getRootAlias($qb);

        switch ($operator) {
            case Operators::IN_LIST:
                $qb->andWhere($qb->expr()->in(sprintf('%s.code', $alias), $value));
                break;
            case Operators::EQUALS:
                $qb->andWhere($qb->expr()->eq(sprintf('%s.code', $alias), $value));
                break;
            default:
                break;
        }

        return $qb;
    }

    /**
     * @param string                 $operator
     * @param string                 $field
     * @param string|array|\DateTime $value
     *
     * @return AbstractFilter
     */
    protected function checkValue($operator, $field, $value): AbstractFilter
    {
        switch ($operator) {
            case Operators::IN_LIST:
                if (!is_array($value)) {
                    throw InvalidPropertyTypeException::arrayExpected($field, static::class, $value);
                }
                break;
            case Operators::EQUALS:
                if (!is_string($value)) {
                    throw InvalidPropertyTypeException::stringExpected($field, static::class, $value);
                }
                break;
            default:
                $this->throwOperatorNotSupportedException($operator, $field);
        }

        return $this;
    }
}
