<?php

namespace App\Service\Image\MessageHandler\Event;

use App\Service\Image\ImagePicturePreviewService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Service\Image\Message\Event\PictureUrlUpdatedEvent;
use Exception;

class DownloadPreviewImageToFilesystem implements MessageHandlerInterface
{
    /** @var ImagePicturePreviewService */
    private $imagePicturePreviewService;

    /**
     * DownloadPreviewImageToFilesystem constructor.
     * @param ImagePicturePreviewService $imagePicturePreviewService
     */
    public function __construct(ImagePicturePreviewService $imagePicturePreviewService)
    {
        $this->imagePicturePreviewService = $imagePicturePreviewService;
    }

    /**
     * @param PictureUrlUpdatedEvent $event
     * @throws Exception
     */
    public function __invoke(PictureUrlUpdatedEvent $event)
    {
        $this->imagePicturePreviewService->downloadFile(
            $event->getImageId(),
            $event->getNewPictureUrl()
        );
    }
}
