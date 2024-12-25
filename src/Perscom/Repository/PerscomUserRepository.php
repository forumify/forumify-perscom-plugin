<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;

/**
 * @extends AbstractRepository<PerscomUser>
 */
class PerscomUserRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly PerscomFactory $perscomFactory,
    ) {
        parent::__construct($managerRegistry);
    }

    public static function getEntityClass(): string
    {
        return PerscomUser::class;
    }
}

