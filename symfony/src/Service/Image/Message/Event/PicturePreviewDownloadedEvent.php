<?php

namespace App\Service\Image\Message\Event;

class PicturePreviewDownloadedEvent
{
    /** @var string */
    private $imageId;

    /** @var string */
    private $newImagePath;

    /**
     * PreviewImageDownloadedEvent constructor.
     * @param string $imageId
     * @param string $newImagePath
     */
    public function __construct(string $imageId, string $newImagePath)
    {
        $this->imageId = $imageId;
        $this->newImagePath = $newImagePath;
    }

    /**
     * @return string
     */
    public function getImageId(): string
    {
        return $this->imageId;
    }

    /**
     * @return string
     */
    public function getNewImagePath(): string
    {
        return $this->newImagePath;
    }
}
