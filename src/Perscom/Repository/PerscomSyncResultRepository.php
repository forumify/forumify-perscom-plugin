<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use DateInterval;
use DateTime;
use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomSyncResult;
use Throwable;

/**
 * @extends AbstractRepository<PerscomSyncResult>
 */
class PerscomSyncResultRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return PerscomSyncResult::class;
    }

    public function deleteRunningResults(): void
    {
        try {
            $this->createQueryBuilder('r')
                ->delete(PerscomSyncResult::class, 'r')
                ->where('r.end IS NULL')
                ->andWhere('r.start < :timeout')
                ->setParameter('timeout', new DateTime()->sub(new DateInterval('PT1H')))
                ->getQuery()
                ->execute()
            ;
        } catch (Throwable) {
            // ok
        }
    }

    public function deleteOldResults(): void
    {
        try {
            $this->createQueryBuilder('c')
                ->delete(PerscomSyncResult::class, 'r')
                ->where('r.start < :timeout')
                ->setParameter('timeout', new DateTime()->sub(new DateInterval('P14D')))
                ->getQuery()
                ->execute()
            ;
        } catch (Throwable) {
            // ok
        }
    }
}
