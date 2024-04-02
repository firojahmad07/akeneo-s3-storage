<?php

namespace Spygar\Bundle\AkeneoS3StorageBundle\FileStorage\File;

use Akeneo\Tool\Component\FileStorage\Exception\FileAlreadyExistsException;
use Akeneo\Tool\Component\FileStorage\Exception\FileRemovalException;
use Akeneo\Tool\Component\FileStorage\File\FileStorer as baseFileStorer;
use Akeneo\Tool\Component\FileStorage\Exception\FileTransferException;
use Akeneo\Tool\Component\FileStorage\Exception\InvalidFile;
use Akeneo\Tool\Component\FileStorage\FileInfoFactoryInterface;
use Akeneo\Tool\Component\FileStorage\FilesystemProvider;
use Akeneo\Tool\Component\FileStorage\Model\FileInfoInterface;
use Akeneo\Tool\Component\StorageUtils\Exception\DuplicateObjectException;
use Akeneo\Tool\Component\StorageUtils\Saver\SaverInterface;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToWriteFile;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Akeneo\Tool\Component\FileStorage\File\FileStorerInterface;
use Spygar\Bundle\AkeneoS3StorageBundle\FileStorage\S3FileSystem;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/**
 * Move a raw file to the storage destination filesystem
 * transforms it as a \Spygar\Bundle\AkeneoS3StorageBundle\FileStorage\Model\FileInfoInterface
 * and save it to the database.
 *
 * @author Firoj Ahmad
 */
class FileStorer extends baseFileStorer implements FileStorerInterface
{
    public function __construct(
        private FilesystemProvider $filesystemProvider,
        private SaverInterface $saver,
        private FileInfoFactoryInterface $factory,
        private S3FileSystem $s3StorerFileSystem,
    ) {
        parent::__construct($filesystemProvider, $saver, $factory);
    }

    /**
     * {@inheritdoc}
     */
    public function store(\SplFileInfo $localFile, string $destFsAlias, bool $deleteRawFile = false): FileInfoInterface
    {
        if (!is_file($localFile->getPathname())) {
            throw new InvalidFile(sprintf('The file "%s" does not exist.', $localFile->getPathname()));
        }
        $filesystem = $this->filesystemProvider->getFilesystem($destFsAlias);

        $s3ilesystem = $this->s3StorerFileSystem->getFilesystem();
        $file = $this->factory->createFromRawFile($localFile, $destFsAlias);

        $error = sprintf(
            'Unable to move the file "%s" to the "%s" filesystem.',
            $localFile->getPathname(),
            $destFsAlias
        );

        if (false === $resource = fopen($localFile->getPathname(), 'r')) {
            throw new FileTransferException($error);
        }
        try {
            $options = [];
            $mimeType = $file->getMimeType();
            if (null !== $mimeType) {
                $options['ContentType'] = $mimeType;
                $options['metadata']['contentType'] = $mimeType;
                $options['ACL'] = 'public-read';
            }
            if ($filesystem->fileExists($file->getKey())) {
                throw UnableToWriteFile::atLocation($file->getKey(), 'The file already exists');
            }
            $s3ilesystem->writeStream($file->getKey(), $resource, $options);
            $filesystem->writeStream($file->getKey(), $resource, $options);
        } catch (FilesystemException $e) {
            throw new FileTransferException($error, $e->getCode(), $e);
        }

        try {
            $this->saver->save($file);
        } catch (DuplicateObjectException $e) {
            throw new FileAlreadyExistsException($e->getMessage());
        }

        if (true === $deleteRawFile) {
            $this->deleteRawFile($localFile, $filesystem);
        }
        
        return $file;
    }

    /**
     * @param \SplFileInfo $file
     *
     * @throws FileRemovalException
     */
    private function deleteRawFile(\SplFileInfo $file, $filesystem): void
    {
        try {
            $filesystem->remove($file->getPathname());
        } catch (IOException $e) {
            throw new FileRemovalException(
                sprintf('Unable to delete the file "%s".', $file->getPathname()),
                $e->getCode(),
                $e
            );
        }
    }
}
