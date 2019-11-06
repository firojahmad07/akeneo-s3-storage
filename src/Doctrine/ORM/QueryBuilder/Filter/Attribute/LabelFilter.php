<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\Doctrine\ORM\QueryBuilder\Filter\Attribute;

use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Doctrine\ORM\QueryBuilder;
use Ewave\Bundle\CoreBundle\Doctrine\ORM\QueryBuilder\Filter\AbstractFilter;

/**
 * Class LabelFilter
 *
 * @package Ewave\Bundle\AttributeBundle\Doctrine\ORM\QueryBuilder\Filter\Attribute
 */
class LabelFilter extends AbstractFilter
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

        $localeCodes = $context['locales'] ?? [];
        switch ($operator) {
            case Operators::ALL_COMPLETE:
                $qb->leftJoin(
                    sprintf('%s.translations', $alias),
                    'at',
                    'WITH',
                    $qb->expr()->in('at.locale', $localeCodes)
                );
                $localeNumber = count($localeCodes);
                $qb->andHaving($qb->expr()->eq($qb->expr()->count('at.id'), $localeNumber));
                $qb->addGroupBy(sprintf('%s.id', $alias));
                break;
            case Operators::AT_LEAST_INCOMPLETE:
                $qb->leftJoin(
                    sprintf('%s.translations', $alias),
                    'at',
                    'WITH',
                    $qb->expr()->in('at.locale', $localeCodes)
                );
                $localeNumber = count($localeCodes);
                $qb->andHaving($qb->expr()->lt($qb->expr()->count('at.id'), $localeNumber));
                $qb->addGroupBy(sprintf('%s.id', $alias));
                break;
            case Operators::ALL_INCOMPLETE:
                $qb->leftJoin(
                    sprintf('%s.translations', $alias),
                    'at',
                    'WITH',
                    $qb->expr()->in('at.locale', $localeCodes)
                );
                $localeNumber = 0;
                $qb->andHaving($qb->expr()->eq($qb->expr()->count('at.id'), $localeNumber));
                $qb->addGroupBy(sprintf('%s.id', $alias));
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
            case Operators::ALL_COMPLETE:
            case Operators::AT_LEAST_INCOMPLETE:
            case Operators::ALL_INCOMPLETE:
                break;
            default:
                $this->throwOperatorNotSupportedException($operator, $field);
        }

        return $this;
    }
}
