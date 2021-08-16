<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $picture_title;

    /**
     * @ORM\Column(type="text", length=3000, nullable=false)
     */
    private $picture_url;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $picture_description;

    /**
     * @ORM\Column(type="text", length=3000, nullable=true)
     */
    private $picture_preview;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPictureTitle(): ?string
    {
        return $this->picture_title;
    }

    public function setPictureTitle(string $picture_title): self
    {
        $this->picture_title = $picture_title;

        return $this;
    }

    public function getPictureUrl(): ?string
    {
        return $this->picture_url;
    }

    public function setPictureUrl(string $picture_url): self
    {
        $this->picture_url = $picture_url;

        return $this;
    }

    public function getPictureDescription(): ?string
    {
        return $this->picture_description;
    }

    public function setPictureDescription(?string $picture_description): self
    {
        $this->picture_description = $picture_description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPicturePreview()
    {
        return $this->picture_preview;
    }

    /**
     * @param mixed $picture_preview
     */
    public function setPicturePreview($picture_preview): void
    {
        $this->picture_preview = $picture_preview;
    }
}
