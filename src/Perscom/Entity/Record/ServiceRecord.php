<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\ServiceRecordRepository;
use Perscom\Contracts\ResourceContract;

#[ORM\Entity(repositoryClass: ServiceRecordRepository::class)]
#[ORM\Table('perscom_record_service')]
class ServiceRecord implements RecordInterface
{
    use RecordFields;

    public static function getPerscomResource(Perscom $perscom): ResourceContract
    {
        return $perscom->serviceRecords();
    }
}
