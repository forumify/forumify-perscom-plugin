<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;

class QualificationRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return Qualification::class;
    }
}
