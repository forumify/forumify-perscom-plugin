<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AwardRecord;

class AwardRecordRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return AwardRecord::class;
    }
}
