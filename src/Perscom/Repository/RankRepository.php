<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Rank;

class RankRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return Rank::class;
    }
}
