<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\ServiceRecordRepository;
use Perscom\Contracts\ResourceContract;

#[ORM\Entity(repositoryClass: ServiceRecordRepository::class)]
#[ORM\Table('perscom_record_service')]
class ServiceRecord implements RecordInterface
{
    use RecordFields;

    #[ORM\ManyToOne(targetEntity: PerscomUser::class, inversedBy: 'serviceRecords', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PerscomUser $user;

    public static function getPerscomResource(Perscom $perscom): ResourceContract
    {
        return $perscom->serviceRecords();
    }
}
