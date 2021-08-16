<?php

namespace App\Service\Image;

use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Image;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use App\Service\Parser\Value\AllowedMimeTypes;

class ImagePicturePreviewService
{
    /** @var array  */
    private const ALLOWED_MIME_TYPES = AllowedMimeTypes::TYPE_IMAGE;

    /** @var string  */
    private const HTTP_REQUEST_METHOD = 'GET';

    /** @var string  */
    private const FILE_DIR_FINAL = 'bulkupload';

    /** @var Filesystem */
    private $filesystem;

    /** @var HttpClientInterface */
    private $httpClient;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ImageRepository */
    private $imageRepository;

    /** @var ImageEventDispatcherService */
    private $imageEventDispatcherService;

    /**
     * ImageDownloadedFileFieldService constructor.
     * @param Filesystem $filesystem
     * @param HttpClientInterface $httpClient
     * @param EntityManagerInterface $entityManager
     * @param ImageRepository $imageRepository
     * @param ImageEventDispatcherService $imageEventDispatcherService
     */
    public function __construct(
        Filesystem $filesystem,
        HttpClientInterface $httpClient,
        EntityManagerInterface $entityManager,
        ImageRepository $imageRepository,
        ImageEventDispatcherService $imageEventDispatcherService
    ) {
        $this->filesystem = $filesystem;
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->imageRepository = $imageRepository;
        $this->imageEventDispatcherService = $imageEventDispatcherService;
    }

    /**
     * @param string $imageId
     * @param string $url
     * @throws Exception
     */
    public function downloadFile(string $imageId, string $url): void
    {
        try {
            $image = $this->imageRepository->findById($imageId);
            if (!$image instanceof Image) {
                throw new \LogicException('Not an image');
            }
            $this->validateUrlOrThrow($image->getPictureUrl());

            $originalFileName = basename($url);
            if (trim($originalFileName) === '') {
                throw new \Exception ('Invalid file name');
            }
            $directoryWhereToUploadTemp = sys_get_temp_dir();
            $uploadFilePathTemp = $directoryWhereToUploadTemp . '/' . substr(sha1(mt_rand()), 0, 15);
            $response = $this->httpClient->request(self::HTTP_REQUEST_METHOD, $url);

            $this->filesystem->mkdir($directoryWhereToUploadTemp);
            $this->deleteFileIfExists($uploadFilePathTemp);
            $this->filesystem->touch($uploadFilePathTemp);
            $this->filesystem->appendToFile($uploadFilePathTemp, $response->getContent());

            $this->validateUploadedFileOrThrow($originalFileName, $uploadFilePathTemp);

            $directoryWhereToUploadFinal = sys_get_temp_dir() . '/' . self::FILE_DIR_FINAL;
            $this->filesystem->mkdir($directoryWhereToUploadFinal);
            $uploadFilePathFinal = $directoryWhereToUploadFinal . '/' . bin2hex(random_bytes(10)) . '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);
            $this->deleteFileIfExists($uploadFilePathFinal);
            $this->filesystem->rename($uploadFilePathTemp, $uploadFilePathFinal);
            $this->imageEventDispatcherService->afterPreviewImageDownloaded($imageId, $uploadFilePathFinal);
        } catch (\Throwable $exception) {
            if (isset($uploadFilePathTemp)) {
                $this->deleteFileIfExists($uploadFilePathTemp);
            }
            throw new \Exception('Could not download file: '  . $exception->getMessage());
        }
    }

    /**
     * @param string $imageId
     * @param string $url
     * @throws NonUniqueResultException
     */
    public function updateStorage(string $imageId, string $url): void
    {
        $image = $this->imageRepository->findById($imageId);
        if (!$image instanceof Image) {
            throw new \LogicException('Not an image');
        }
        $this->validateUrlOrThrow($image->getPictureUrl());

        $image->setPicturePreview($url);
        $this->entityManager->persist($image);
        $this->entityManager->flush();
    }

    /**
     * @param int $id
     * @return BinaryFileResponse
     * @throws NonUniqueResultException
     */
    public function getBinaryPreviewImageOrThrow(int $id): BinaryFileResponse
    {
        $file = $this->imageRepository->findById($id);
        if (!$file instanceof Image) {
            throw new \LogicException('Not an image');
        }
        $path = $file->getPicturePreview() ?? '';
        if (!$this->filesystem->exists($path)) {
            throw new \LogicException('Image is not on the filesystem');
        }
        return new BinaryFileResponse($path);
    }

    /**
     * @param string $path
     */
    public function removeFromFilesystem(string $path): void
    {
        $this->filesystem->remove($path);
    }

    /**
     * @param string $path
     */
    private function deleteFileIfExists(string $path): void
    {
        if ($this->filesystem->exists($path)) {
            $this->filesystem->remove($path);
        }
    }

    /**
     * @param string $originalFileName
     * @param string $uploadFilePathTemp
     */
    private function validateUploadedFileOrThrow(string $originalFileName, string $uploadFilePathTemp): void
    {
        $mimeTypes = new MimeTypes();
        $originalFileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        if (trim($originalFileExtension) === '') {
            throw new \LogicException('File does not have an extension');
        }

        $detectedMimeType = $mimeTypes->guessMimeType($uploadFilePathTemp);
        if (!in_array($detectedMimeType, self::ALLOWED_MIME_TYPES, true)) {
            $message = sprintf('Unrecognized mime type "%s" , please use: %s',
                $detectedMimeType,
                implode(', ', self::ALLOWED_MIME_TYPES)
            );
            throw new \LogicException($message);
        }

        $knownMimeExtensions = $mimeTypes->getExtensions($detectedMimeType);
        if (!in_array($originalFileExtension, $knownMimeExtensions, true)) {
            $message = sprintf('File extension "%s" does not match mime type "%s" , please use: %s',
                $originalFileExtension,
                $detectedMimeType,
                implode(', ', $knownMimeExtensions)
            );
            throw new \LogicException($message);
        }
    }

    /**
     * @param string $file
     * @return string
     */
    private function getPathFromPreviewImage(string $file): string
    {
        return sys_get_temp_dir() . '/' . self::FILE_DIR_FINAL . '/' . trim($file,'/');
    }

    /**
     * @param string $url
     */
    private function validateUrlOrThrow(string $url): void
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            throw new \LogicException('Url is invalid');
        }
    }
}
