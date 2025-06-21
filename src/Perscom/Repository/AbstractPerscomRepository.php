<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
     * @return T[]
     */
    public function findByPerscomIds(array $perscomIds): array
    {
        return $this->findBy(['perscomId' => $perscomIds]);
    }

    /**
     * Guesses the next ID that PERSCOM will generate.
     * This should only be used when creating relationships without waiting for the PERSCOM sync to happen.
     * When calling this function without flushing after every save, you will get the same ID for all the records.
     */
    public function guessNextPerscomId(): int
    {
        try {
            $max = $this
                ->createQueryBuilder('e')
                ->select('MAX(e.perscomId)')
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (NoResultException|NonUniqueResultException) {
            return 1;
        }

        return $max + 1;
    }
}
