<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;

/**
 * @extends AbstractPerscomRepository<ServiceRecord>
 */
class ServiceRecordRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return ServiceRecord::class;
    }
}
