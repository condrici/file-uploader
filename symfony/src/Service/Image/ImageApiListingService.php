<?php

namespace App\Service\Image;

use App\Repository\ImageRepository;
use App\Entity\Image;

class ImageApiListingService
{
    /** @var ImageRepository */
    private $imageRepository;

    /**
     * ImageApiListingService constructor.
     * @param ImageRepository $imageRepository
     */
    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    /**
     * @return array
     */
    public function listAll(): array
    {
        $items = [];
        $images = $this->getAllImages();
        foreach ($images as $image) {
            $items[] = [
                'picture_id' => $image->getId(),
                'picture_title' => $image->getPictureTitle(),
                'picture_description' => $image->getPictureDescription()
            ];
        }

        return $items;
    }

    /**
     * @return Image[]
     */
    private function getAllImages(): array
    {
        $limit = 100;
        $counter = 0;
        $resultBag = [];

        do {
            $items = $this->imageRepository->findAllWithPagination($counter * $limit, $limit);
            $resultBag[] = $items;
            $counter++;
        } while(count($items) > 0);

        return array_merge([], ...$resultBag);
    }
}
