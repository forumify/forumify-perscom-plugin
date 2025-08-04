<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomEntityInterface;

/**
 * @template T of PerscomEntityInterface
 * @extends AbstractRepository<T>
 */
abstract class AbstractPerscomRepository extends AbstractRepository
{
    /**
     * @return T|null
     */
    public function findOneByPerscomId(int $perscomId): ?object
    {
        return $this->findOneBy(['perscomId' => $perscomId]);
    }

    /**
     * @param array<int> $perscomIds
     * @return array<T>
     */
    public function findByPerscomIds(array $perscomIds): array
    {
        return $this->findBy(['perscomId' => $perscomIds]);
    }
}
