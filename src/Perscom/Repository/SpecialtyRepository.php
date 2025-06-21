<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Specialty;

class SpecialtyRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return Specialty::class;
    }
}
