<?php

namespace App\Service\Image;

use App\Service\Image\Message\Event\PictureUrlUpdatedEvent;
use App\Service\Image\Message\Event\PicturePreviewDownloadedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class ImageEventDispatcherService
{
    /** @var MessageBusInterface */
    private $messageBus;

    /**
     * ImageManagerService constructor.
     * @param MessageBusInterface $messageBus
     */
    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @param string $imageId
     * @param string $previousPictureUrl
     * @param string $newPictureUrl
     */
    public function afterPictureUrlUpdate(string $imageId, string $previousPictureUrl, string $newPictureUrl): void
    {
        $event = new PictureUrlUpdatedEvent($imageId, $previousPictureUrl, $newPictureUrl);
        $this->messageBus->dispatch($event);
    }

    /**
     * @param string $imageId
     * @param string $newImagePath
     */
    public function afterPreviewImageDownloaded(string $imageId, string $newImagePath): void
    {
        $event = new PicturePreviewDownloadedEvent($imageId, $newImagePath);
        $this->messageBus->dispatch($event);
    }
}
