<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;

class UnitRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return Unit::class;
    }
}
