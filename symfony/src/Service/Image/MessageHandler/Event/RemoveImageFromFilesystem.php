<?php

namespace App\Service\Image\MessageHandler\Event;

use App\Service\Image\ImagePicturePreviewService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Service\Image\Message\Event\PictureUrlUpdatedEvent;

class RemoveImageFromFilesystem implements MessageHandlerInterface
{
    /** @var ImagePicturePreviewService */
    private $imagePicturePreviewService;

    /**
     * RemoveImageFromFilesystem constructor.
     * @param ImagePicturePreviewService $imagePicturePreviewService
     */
    public function __construct(ImagePicturePreviewService $imagePicturePreviewService)
    {
        $this->imagePicturePreviewService = $imagePicturePreviewService;
    }

    /**
     * @param PictureUrlUpdatedEvent $event
     */
    public function __invoke(PictureUrlUpdatedEvent $event)
    {
        if (trim($event->getPreviousPictureUrl()) === '') {
            return;
        }
        $this->imagePicturePreviewService->removeFromFilesystem(
            $event->getPreviousPictureUrl()
        );
    }
}
