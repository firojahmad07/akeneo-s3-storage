<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\JobParameters\ConstraintCollectionProvider;

use Akeneo\Pim\Enrichment\Component\Product\Validator\Constraints\WritableDirectory;
use Akeneo\Tool\Component\Batch\Job\JobInterface;
use Akeneo\Tool\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class CsvAttributeExport
 *
 * @package Ewave\Bundle\AttributeBundle\JobParameters\ConstraintCollectionProvider
 */
class CsvAttributeExport implements ConstraintCollectionProviderInterface
{
    /** @var array */
    protected $supportedJobNames;

    /**
     * @param array $supportedJobNames
     */
    public function __construct(array $supportedJobNames)
    {
        $this->supportedJobNames = $supportedJobNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintCollection()
    {
        return new Collection(
            [
                'fields' => [
                    'filePath'              => [
                        new NotBlank(['groups' => ['Execution', 'FileConfiguration']]),
                        new WritableDirectory(['groups' => ['Execution', 'FileConfiguration']]),
                        new Regex(
                            [
                                'pattern' => '/.\.csv$/',
                                'message' => 'The extension file must be ".csv"',
                            ]
                        ),
                    ],
                    'delimiter'             => [
                        new NotBlank(['groups' => ['Default', 'FileConfiguration']]),
                        new Choice(
                            [
                                'choices' => [",", ";", "|"],
                                'message' => 'The value must be one of , or ; or |',
                                'groups'  => ['Default', 'FileConfiguration'],
                            ]
                        ),
                    ],
                    'enclosure'             => [
                        [
                            new NotBlank(['groups' => ['Default', 'FileConfiguration']]),
                            new Choice(
                                [
                                    'choices' => ['"', "'"],
                                    'message' => 'The value must be one of " or \'',
                                    'groups'  => ['Default', 'FileConfiguration'],
                                ]
                            ),
                        ],
                    ],
                    'withHeader'            => new Type(
                        [
                            'type'   => 'bool',
                            'groups' => ['Default', 'FileConfiguration'],
                        ]
                    ),
                    'user_to_notify'        => new Type('string'),
                    'is_user_authenticated' => new Type('bool'),
                    'filters'               => new Collection([]),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
