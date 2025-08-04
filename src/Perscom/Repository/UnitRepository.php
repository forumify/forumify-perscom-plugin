<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Unit;

/**
 * @extends AbstractPerscomRepository<Unit>
 */
class UnitRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return Unit::class;
    }
}
