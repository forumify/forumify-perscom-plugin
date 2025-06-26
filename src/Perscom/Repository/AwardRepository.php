<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Award;

/**
 * @extends AbstractPerscomRepository<Award>
 */
class AwardRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return Award::class;
    }
}
