<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Roster;

/**
 * @extends AbstractPerscomRepository<Roster>
 */
class RosterRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return Roster::class;
    }
}
