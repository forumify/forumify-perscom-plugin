<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;

trait PerscomImageTrait
{
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $imageId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $imageDirty = true;

    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    public function setImageId(?int $imageId): void
    {
        $this->imageId = $imageId;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function isImageDirty(): bool
    {
        return $this->imageDirty;
    }

    public function setImageDirty(bool $imageDirty = true): void
    {
        $this->imageDirty = $imageDirty;
    }
}
