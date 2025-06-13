<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

interface PerscomEntityInterface
{
    public function getPerscomId(): ?int;
    public function setPerscomId(int $id): void;
}
