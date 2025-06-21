<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Qualification;

class QualificationRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return Qualification::class;
    }
}
