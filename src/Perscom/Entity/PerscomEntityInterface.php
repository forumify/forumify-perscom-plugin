<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use DateTime;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Perscom\Contracts\Batchable;
use Perscom\Contracts\Crudable;

interface PerscomEntityInterface
{
    public static function getPerscomResource(Perscom $perscom): Crudable|Batchable;

    public function getId(): int;

    public function getPerscomId(): ?int;

    public function setPerscomId(int $id): void;

    public function isDirty(): bool;

    public function setDirty(bool $dirty = true): void;

    public function getCreatedAt(): ?DateTime;

    public function getUpdatedAt(): ?DateTime;
}
