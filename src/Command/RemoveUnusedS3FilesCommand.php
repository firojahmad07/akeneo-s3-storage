<?php

namespace Spygar\Bundle\AkeneoS3StorageBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Spygar\Bundle\AkeneoS3StorageBundle\FileStorage\S3FileSystem;
use Akeneo\Tool\Component\FileStorage\FilesystemProvider;

class RemoveUnusedS3FilesCommand extends Command
{
    private S3FileSystem $s3FileSystem;
    
    private FilesystemProvider $filesystemProvider;
    private $fileInfoRepository;
    private $attributeRepository;
    private $productQueryBuilderFactory;
    private $productModelQueryBuilderFactory;
    
    public function __construct(
        S3FileSystem $s3FileSystem,
        FilesystemProvider $filesystemProvider,
        $fileInfoRepository,
        $attributeRepository,
        $productQueryBuilderFactory, 
        $productModelQueryBuilderFactory
    ) {
        $this->s3FileSystem = $s3FileSystem;
        $this->filesystemProvider = $filesystemProvider;
        $this->fileInfoRepository = $fileInfoRepository;
        $this->attributeRepository = $attributeRepository;
        $this->productQueryBuilderFactory = $productQueryBuilderFactory;
        $this->productModelQueryBuilderFactory = $productModelQueryBuilderFactory;

        parent::__construct();
    }
    protected function configure()
    {
        $this
            ->setName('spygar:remove_s3_files:unused')
            ->setDescription('Remove unsed files from filesystem')
            ->setHelp('Remove unsed files from filesystem. product images could not be restored from history after that.');
    }

    protected $commandExecutor;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Starting files check.</info>');
        $mediaAttribute = $this->attributeRepository->findMediaAttributeCodes();
        $usedFiles = [];
        $pqb = $this->productModelQueryBuilderFactory->create([]);
        $productsCursor = $pqb->execute();
        foreach ($productsCursor as $product) {
            $rawValues = $product->getRawValues();
            foreach ($mediaAttribute as $attribute) {
                if (!empty($rawValues[$attribute]['<all_channels>']['<all_locales>'])) {
                    $val = $rawValues[$attribute]['<all_channels>']['<all_locales>'];
                    if (gettype($val) == 'string') {
                        $usedFiles[] = $val;
                    }
                }
            }
        }

        $pqb = $this->productQueryBuilderFactory->create([]);
        $productsCursor = $pqb->execute();
        foreach ($productsCursor as $product) {
            $rawValues = $product->getRawValues();
            foreach ($mediaAttribute as $attribute) {
                if (!empty($rawValues[$attribute]['<all_channels>']['<all_locales>'])) {
                    $val = $rawValues[$attribute]['<all_channels>']['<all_locales>'];
                    if (gettype($val) == 'string') {
                        $usedFiles[] = $val;
                    }
                }
            }
        }
        
        $unusedFiles = $this->getUnusedFiles($usedFiles);        
        $deleteDatabaseKey = [];
        $s3FileSystemInstance = $this->s3FileSystem->getFileSystem();
        $filesystemInstance = $this->filesystemProvider->getFilesystem('catalogStorage');
        
        foreach ($unusedFiles as $unusedFileData) {
           $deleteDatabaseKey[] = $unusedFileData['key'];
           $s3FileSystemInstance->delete($unusedFileData['key']);
           $filesystemInstance->delete($unusedFileData['key']);

        }
        $output->writeln('Removed Unused Files from local system and S3 Bucket : ' . count($deleteDatabaseKey));

        $this->deleteFilesByKeys($deleteDatabaseKey);

        return Command::SUCCESS;
    }

    public function getUnusedFiles(array $fileKeys)
    {
        $fileInfoQB = $this->fileInfoRepository->createQueryBuilder('f')
                ->select('f.id, f.key')
                ->where("f.key NOT IN(:fileKeys)")
                ->setParameter('fileKeys', $fileKeys);

        return $fileInfoQB->getQuery()->getResult();
    }

    public function deleteFilesByKeys(array $fileKeys)
    {
        $qb = $this->fileInfoRepository->createQueryBuilder('f')
            ->delete()
            ->where('f.key IN(:fileKeys)')
            ->setParameter('fileKeys', $fileKeys);

        return $qb->getQuery()->execute();
    }
}
