<?php

namespace Spygar\Bundle\AkeneoS3StorageBundle\FileStorage;

use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Visibility;
use League\Flysystem\Filesystem;
use Aws\S3\S3Client;
use Symfony\Component\Filesystem\Filesystem as BaseFileSystem;

/**
 * Move a raw file to the storage destination filesystem
 * and save it to the database.
 *
 * @author Firoj Ahmad
 */
class S3FileSystem
{
    const HTTPS = 'https://';
    const AWS = '.amazonaws.com/';

    private $client;
    public function __construct(
        private $bucketName,
        private $region,
        private $accessKey,
        private $accessSecret
    ) {
        $this->client = new S3Client([
            'version' => 'latest',
            'region'  => $this->region,
            'credentials' => [
                'key'    => $this->accessKey,
                'secret' => $this->accessSecret,
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileSystem(): Filesystem
    {
        $adapter = new AwsS3V3Adapter(
            $this->client,
            $this->bucketName,
            '',
            new PortableVisibilityConverter(Visibility::PUBLIC)
        );

        return new Filesystem($adapter);
    }

    /** Get Public url for preview images */
    public function getS3FilePreviewURL($filename)
    {
        return self::HTTPS . $this->bucketName .'.s3.'. $this->region . self::AWS . $filename;
    }
}