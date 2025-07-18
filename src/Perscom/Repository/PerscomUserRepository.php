<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;

/**
 * @extends AbstractPerscomRepository<PerscomUser>
 */
class PerscomUserRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return PerscomUser::class;
    }
}
