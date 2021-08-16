<?php

namespace App\Service\Image\MessageHandler\Event;

use App\Service\Image\ImagePicturePreviewService;
use App\Service\Image\Message\Event\PicturePreviewDownloadedEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Doctrine\ORM\NonUniqueResultException;

class UpdatePicturePreviewInStorage implements MessageHandlerInterface
{
    /** @var ImagePicturePreviewService */
    private $imagePicturePreviewService;

    /**
     * UpdatePicturePreviewInStorage constructor.
     * @param ImagePicturePreviewService $imagePicturePreviewService
     */
    public function __construct(ImagePicturePreviewService $imagePicturePreviewService)
    {
        $this->imagePicturePreviewService = $imagePicturePreviewService;
    }

    /**
     * @param PicturePreviewDownloadedEvent $event
     * @throws NonUniqueResultException
     */
    public function __invoke(PicturePreviewDownloadedEvent $event): void
    {
        $this->imagePicturePreviewService->updateStorage(
            $event->getImageId(),
            $event->getNewImagePath()
        );
    }
}
