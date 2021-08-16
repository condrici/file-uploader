<?php

namespace App\Service\Image\Message\Event;

class PictureUrlUpdatedEvent
{
    /** @var string */
    private $imageId;

    /** @var string */
    private $previousPictureUrl;

    /** @var string */
    private $newPictureUrl;

    /**
     * ImagePictureUrlUpdatedEvent constructor.
     * @param string $imageId
     * @param string $previousPictureUrl
     * @param string $newPictureUrl
     */
    public function __construct(string $imageId, string $previousPictureUrl, string $newPictureUrl)
    {
        $this->imageId = $imageId;
        $this->previousPictureUrl = $previousPictureUrl;
        $this->newPictureUrl = $newPictureUrl;
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
    public function getPreviousPictureUrl(): string
    {
        return $this->previousPictureUrl;
    }

    /**
     * @return string
     */
    public function getNewPictureUrl(): string
    {
        return $this->newPictureUrl;
    }
}
