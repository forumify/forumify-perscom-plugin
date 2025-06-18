<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;

/**
 * @extends AbstractRepository<PerscomUser>
 */
class PerscomUserRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return PerscomUser::class;
    }

    /**
     * @param array<int|string> $perscomIds
     * @return array<PerscomUser>
     */
    public function findByPerscomIds(array $perscomIds): array
    {
        return $this->findBy(['perscomId' => $perscomIds]);
    }
}
