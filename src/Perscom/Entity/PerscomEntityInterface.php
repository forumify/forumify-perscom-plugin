<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Forumify\PerscomPlugin\Perscom\Perscom;
use Perscom\Contracts\ResourceContract;

interface PerscomEntityInterface
{
    public function getId(): int;
    public function getPerscomId(): ?int;
    public function setPerscomId(int $id): void;
    public static function getPerscomResource(Perscom $perscom): ResourceContract;
}
