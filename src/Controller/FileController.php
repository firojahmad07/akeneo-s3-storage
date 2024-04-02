<?php

namespace Spygar\Bundle\AkeneoS3StorageBundle\Controller;

use Spygar\Bundle\AkeneoS3StorageBundle\FileStorage\S3FileSystem;
use Akeneo\Pim\Enrichment\Bundle\Controller\Ui\FileController as BaseFileController;
use Akeneo\Pim\Enrichment\Bundle\File\DefaultImageProviderInterface;
use Akeneo\Pim\Enrichment\Bundle\File\FileTypeGuesserInterface;
use Akeneo\Pim\Enrichment\Bundle\File\FileTypes;
use Akeneo\Tool\Component\FileStorage\FilesystemProvider;
use Akeneo\Tool\Component\FileStorage\Repository\FileInfoRepositoryInterface;
use Exception;
use Liip\ImagineBundle\Controller\ImagineController;
use Liip\ImagineBundle\Exception\LogicException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FileController extends BaseFileController
{
    const DEFAULT_IMAGE_KEY = '__default_image__';
    const SVG_MIME_TYPES = ['image/svg', 'image/svg+xml'];

    public function __construct(
        protected ImagineController $imagineController,
        protected FilesystemProvider $filesystemProvider,
        protected FileInfoRepositoryInterface $fileInfoRepository,
        protected FileTypeGuesserInterface $fileTypeGuesser,
        protected DefaultImageProviderInterface $defaultImageProvider,
        private S3FileSystem $s3StorerFileSystem,
        protected array $filesystemAliases
    ) {
        parent::__construct($imagineController, $filesystemProvider, $fileInfoRepository, $fileTypeGuesser, $defaultImageProvider, $filesystemAliases);
    }
    public function showAction(Request $request, string $filename, ?string $filter = null): Response
    {
        $filename = urldecode($filename);
        $fileInfo = $this->fileInfoRepository->findOneByIdentifier($filename);
        if (null === $fileInfo) {
            return $this->renderDefaultImage(FileTypes::MISC, $filter);
        }

        $mimeType = $this->getMimeType($filename);
        $fileType = $this->fileTypeGuesser->guess($mimeType);

        if (self::DEFAULT_IMAGE_KEY === $filename || FileTypes::IMAGE !== $fileType) {
            return $this->renderDefaultImage($fileType, $filter);
        }

        if (in_array($mimeType, self::SVG_MIME_TYPES)) {
            return $this->getFileResponse($filename, 'image/svg+xml');
        }
        try {
            return $this->imagineController->filterAction($request, $filename, $filter);
        } catch (NotFoundHttpException | LogicException | \RuntimeException | Exception $exception) {
            return $this->renderDefaultImage(FileTypes::IMAGE, $filter);
        }
    }

    private function getFileResponse(string $filename, string $mimeType): Response
    {
        foreach ($this->filesystemAliases as $alias) {
            $fs = $this->filesystemProvider->getFilesystem($alias);

            $response = new Response($fs->read($filename));
            $response->headers->set('Content-Type', $mimeType);

            return $response;
        }

        throw new NotFoundHttpException(
            sprintf('File with key "%s" could not be found.', $filename)
        );
    }

    public function s3MediaAction($filename)
    {
        $mediaUrl = $this->s3StorerFileSystem->getS3FilePreviewURL(urldecode($filename));

        return new RedirectResponse($mediaUrl, 301);
    }
}
