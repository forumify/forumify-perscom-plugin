<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

interface PerscomEntityWithImageInterface
{
    public function getImageId(): ?int;
    public function setImageId(?int $imageId): void;
    public function getImage(): ?string;
    public function setImage(?string $image): void;
}
