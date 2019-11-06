<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\Doctrine\ORM\QueryBuilder\Filter\Attribute;

use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\Pim\Structure\Bundle\Doctrine\ORM\Repository\AttributeGroupRepository;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Doctrine\ORM\QueryBuilder;
use Ewave\Bundle\CoreBundle\Doctrine\ORM\QueryBuilder\Filter\AbstractFilter;

/**
 * Class AttributeGroupFilter
 *
 * @package Ewave\Bundle\AttributeBundle\Doctrine\ORM\QueryBuilder\Filter\Attribute
 */
class GroupFilter extends AbstractFilter
{
    /**
     * @var AttributeGroupRepository
     */
    protected $attributeGroupRepository;

    /**
     * GroupFilter constructor.
     *
     * @param AttributeGroupRepository $attributeGroupRepository
     * @param array                    $supportedFields
     * @param array                    $supportedOperators
     */
    public function __construct(
        AttributeGroupRepository $attributeGroupRepository,
        array $supportedFields = [],
        array $supportedOperators = []
    ) {
        parent::__construct($supportedFields, $supportedOperators);
        $this->attributeGroupRepository = $attributeGroupRepository;
    }

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
            case Operators::EQUALS:
            case Operators::IN_LIST:
                $groupIds = $this->getGroupIdsByCodes((array)$value);
                $qb->andWhere($qb->expr()->in(sprintf('%s.group', $alias), $groupIds));
                break;
            case Operators::NOT_EQUAL:
            case Operators::NOT_IN_LIST:
                $groupIds = $this->getGroupIdsByCodes((array)$value);
                $qb->andWhere($qb->expr()->notIn(sprintf('%s.group', $alias), $groupIds));
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
            case Operators::NOT_IN_LIST:
                if (!is_array($value)) {
                    throw InvalidPropertyTypeException::arrayExpected($field, static::class, $value);
                }
                break;
            case Operators::EQUALS:
            case Operators::NOT_EQUAL:
                if (!is_string($value)) {
                    throw InvalidPropertyTypeException::stringExpected($field, static::class, $value);
                }
                break;
            default:
                $this->throwOperatorNotSupportedException($operator, $field);
        }

        return $this;
    }

    /**
     * @param array $groupCodes
     *
     * @return array
     */
    protected function getGroupIdsByCodes(array $groupCodes = [])
    {
        if (!$groupCodes) {
            return [];
        }

        $qb = $this->attributeGroupRepository->createQueryBuilder('ag');
        $qb
            ->resetDQLPart('select')
            ->select('ag.id')
            ->where($qb->expr()->in('ag.code', $groupCodes));

        return array_map(
            function ($item) {
                return $item['id'];
            },
            $qb->getQuery()->getArrayResult()
        );
    }
}
