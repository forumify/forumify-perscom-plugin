<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;

/**
 * @extends AbstractRepository<ServiceRecord>
 */
class ServiceRecordRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return ServiceRecord::class;
    }
}
