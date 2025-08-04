<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Status;

/**
 * @extends AbstractPerscomRepository<Status>
 */
class StatusRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return Status::class;
    }
}
