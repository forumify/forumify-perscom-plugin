<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\Roster;

/**
 * @extends AbstractRepository<Roster>
 */
class RosterRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return Roster::class;
    }
}
