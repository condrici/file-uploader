<?php

namespace App\Service\Image;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ImageRepository;
use Doctrine\ORM\NonUniqueResultException;

class ImageCrudService
{
    /** @var ImageRepository */
    private $imageRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ImageEventDispatcherService */
    private $imageManagerService;

    /**
     * ImageCrudService constructor.
     * @param ImageRepository $imageRepository
     * @param EntityManagerInterface $entityManager
     * @param ImageEventDispatcherService $imageManagerService
     */
    public function __construct(
        ImageRepository $imageRepository,
        EntityManagerInterface $entityManager,
        ImageEventDispatcherService $imageManagerService
    ) {
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
        $this->imageManagerService = $imageManagerService;
    }

    /**
     * @param array $records
     * @param bool $downloadFromPictureUrl
     * @throws NonUniqueResultException
     */
    public function createOrUpdate(
        array $records,
        bool $downloadFromPictureUrl = true
    ): void {
        $newPictureTitles = [];
        foreach ($records as $record) {
            $entity = $this->imageRepository->findByPictureTitle($record->picture_title);
            if (!$entity instanceof Image) {
                $entity = new Image();
                $newPictureTitles[] = $record->picture_title;
            }
            if ($downloadFromPictureUrl && $entity->getId() && $record->picture_url !== $entity->getPictureUrl()) {
                $this->imageManagerService->afterPictureUrlUpdate(
                    $entity->getId(), $entity->getPictureUrl(), $record->picture_url
                );
            }

            $entity->setPictureTitle($record->picture_title);
            $entity->setPictureDescription($record->picture_description);
            $entity->setPictureUrl($record->picture_url);

            $this->entityManager->persist($entity);
            $entity->getId();
        }
        $this->entityManager->flush();
        if ($downloadFromPictureUrl) {
            $this->updateNewEntities($newPictureTitles);
        }
    }

    /**
     * @param array $newPictureTitles
     * @throws NonUniqueResultException
     */
    private function updateNewEntities(array $newPictureTitles): void
    {
        foreach ($newPictureTitles as $newPictureTitle) {
            $entity = $this->imageRepository->findByPictureTitle($newPictureTitle);
            if (!$entity instanceof Image) {
                continue;
            }
            $this->imageManagerService->afterPictureUrlUpdate(
                $entity->getId(), '', $entity->getPictureUrl()
            );
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }
}
