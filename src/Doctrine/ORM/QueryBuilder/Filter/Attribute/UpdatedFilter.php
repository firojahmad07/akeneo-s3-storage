<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\Doctrine\ORM\QueryBuilder\Filter\Attribute;

use Akeneo\Pim\Enrichment\Component\Product\Exception\InvalidOptionException;
use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\FieldFilterHelper;
use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\Tool\Component\Batch\Job\BatchStatus;
use Akeneo\Tool\Component\Batch\Job\JobRepositoryInterface;
use Akeneo\Tool\Component\Batch\Model\JobExecution;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyException;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Akeneo\Tool\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\QueryBuilder;
use Ewave\Bundle\CoreBundle\Doctrine\ORM\QueryBuilder\Filter\AbstractFilter;
use Ewave\Bundle\CoreBundle\Helper\DatetimeHelper;

/**
 * Class UpdatedFilter
 *
 * @package Ewave\Bundle\AttributeBundle\Doctrine\ORM\QueryBuilder\Filter\Attribute
 */
class UpdatedFilter extends AbstractFilter
{
    /**
     * @var IdentifiableObjectRepositoryInterface
     */
    protected $jobInstanceRepository;

    /**
     * @var JobRepositoryInterface
     */
    protected $jobRepository;

    /**
     * UpdatedFilter constructor.
     *
     * @param JobRepositoryInterface                $jobRepository
     * @param IdentifiableObjectRepositoryInterface $jobInstanceRepository
     * @param array                                 $supportedFields
     * @param array                                 $supportedOperators
     */
    public function __construct(
        JobRepositoryInterface $jobRepository,
        IdentifiableObjectRepositoryInterface $jobInstanceRepository,
        array $supportedFields = [],
        array $supportedOperators = []
    ) {
        parent::__construct($supportedFields, $supportedOperators);
        $this->jobInstanceRepository = $jobInstanceRepository;
        $this->jobRepository = $jobRepository;
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
            case Operators::LOWER_THAN:
                $qb->andWhere($qb->expr()->lt(sprintf('%s.updated', $alias), ':updated'));
                $qb->setParameter('updated', $this->getDateTimeValue($value, $field), Type::DATETIME);
                break;
            case Operators::LOWER_OR_EQUAL_THAN:
                $qb->andWhere($qb->expr()->lte(sprintf('%s.updated', $alias), ':updated'));
                $qb->setParameter('updated', $this->getDateTimeValue($value, $field), Type::DATETIME);
                break;
            case Operators::GREATER_THAN:
                $qb->andWhere($qb->expr()->gt(sprintf('%s.updated', $alias), ':updated'));
                $qb->setParameter('updated', $this->getDateTimeValue($value, $field), Type::DATETIME);
                break;
            case Operators::GREATER_OR_EQUAL_THAN:
                $qb->andWhere($qb->expr()->gte(sprintf('%s.updated', $alias), ':updated'));
                $qb->setParameter('updated', $this->getDateTimeValue($value, $field), Type::DATETIME);
                break;
            case Operators::SINCE_LAST_N_DAYS:
                $value = (int)$value;
                $updated = new \DateTime(sprintf('%s days ago', $value), DatetimeHelper::getServerTimezone());
                $this->applyFilter($qb, $field, Operators::GREATER_THAN, $updated, $context);
                break;
            case Operators::SINCE_LAST_JOB:
                $lastCompletedJobExecution = $this->getLastCompletedJobExecution($value);
                if ($lastCompletedJobExecution) {
                    $updated = $lastCompletedJobExecution->getStartTime();
                    $this->applyFilter($qb, $field, Operators::GREATER_THAN, $updated, $context);
                }
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
        if (!$value) {
            throw InvalidOptionException::valueNotEmptyExpected($field, static::class);
        }

        switch ($operator) {
            case Operators::LOWER_THAN:
            case Operators::LOWER_OR_EQUAL_THAN:
            case Operators::GREATER_THAN:
            case Operators::GREATER_OR_EQUAL_THAN:
                FieldFilterHelper::checkDateTime(
                    $field,
                    $value,
                    'Y-m-d H:i:s',
                    'yyyy-mm-dd H:i:s',
                    static::class
                );
                break;
            case Operators::SINCE_LAST_N_DAYS:
                if (!is_numeric($value)) {
                    throw InvalidPropertyTypeException::numericExpected($field, static::class, $value);
                }
                break;
            case Operators::SINCE_LAST_JOB:
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
     * @param $code
     *
     * @return JobExecution|null
     */
    public function getLastCompletedJobExecution($code): ?JobExecution
    {
        $jobInstance = $this->jobInstanceRepository->findOneByIdentifier($code);

        if (null === $jobInstance) {
            throw InvalidPropertyException::validEntityCodeExpected(
                'job_instance',
                'code',
                'The job instance does not exist',
                static::class,
                $code
            );
        }

        $lastCompletedJobExecution = $this->jobRepository->getLastJobExecution($jobInstance, BatchStatus::COMPLETED);
        if (null === $lastCompletedJobExecution) {
            return null;
        }

        return $lastCompletedJobExecution;
    }
}