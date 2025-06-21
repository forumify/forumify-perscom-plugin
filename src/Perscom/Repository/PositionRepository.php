<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Position;

class PositionRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return Position::class;
    }
}
