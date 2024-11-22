<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;

/**
 * @template-extends AbstractRepository<Mission>
 */
class MissionRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return Mission::class;
    }
}
