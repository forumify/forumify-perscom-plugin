<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Record\CombatRecord;

/**
 * @extends AbstractPerscomRepository<CombatRecord>
 */
class CombatRecordRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return CombatRecord::class;
    }
}
