<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\JobParameters\DefaultValueProvider;

use Akeneo\Channel\Component\Repository\ChannelRepositoryInterface;
use Akeneo\Channel\Component\Repository\LocaleRepositoryInterface;
use Akeneo\Tool\Component\Batch\Job\JobInterface;
use Akeneo\Tool\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;

/**
 * Class CsvAttributeExport
 *
 * @package Ewave\Bundle\AttributeBundle\JobParameters\DefaultValueProvider
 */
class CsvAttributeExport implements DefaultValuesProviderInterface
{
    /** @var array */
    protected $supportedJobNames;

    /** @var ChannelRepositoryInterface */
    protected $channelRepository;

    /** @var LocaleRepositoryInterface */
    protected $localeRepository;

    /**
     * AttributeExport constructor.
     *
     * @param ChannelRepositoryInterface $channelRepository
     * @param LocaleRepositoryInterface  $localeRepository
     * @param array                      $supportedJobNames
     */
    public function __construct(

        ChannelRepositoryInterface $channelRepository,
        LocaleRepositoryInterface $localeRepository,
        array $supportedJobNames
    ) {
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;
        $this->supportedJobNames = $supportedJobNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValues()
    {
        $channels = $this->channelRepository->getFullChannels();
        $defaultChannelCode = (0 !== count($channels)) ? $channels[0]->getCode() : null;

        $localesCodes = $this->localeRepository->getActivatedLocaleCodes();
        $defaultLocaleCodes = (0 !== count($localesCodes)) ? [$localesCodes[0]] : [];

        return [
            'filePath'              => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'attribute_export_%job_label%_%datetime%.csv',
            'delimiter'             => ';',
            'enclosure'             => '"',
            'withHeader'            => true,
            'user_to_notify'        => null,
            'is_user_authenticated' => false,
            'filters'               => [
                'data'      => [],
                'structure' => [
                    'scope'   => $defaultChannelCode,
                    'locales' => $defaultLocaleCodes,
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
