<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;
use Forumify\PerscomPlugin\Perscom\Entity\Award;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\AwardRecordRepository;
use Perscom\Contracts\Batchable;
use Perscom\Contracts\Crudable;

#[ORM\Entity(repositoryClass: AwardRecordRepository::class)]
#[ORM\Table('perscom_record_award')]
class AwardRecord implements RecordInterface
{
    use RecordFields;

    #[ORM\ManyToOne(targetEntity: PerscomUser::class, inversedBy: 'awardRecords', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PerscomUser $user;

    #[ORM\ManyToOne(targetEntity: Award::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Award $award;

    public static function getPerscomResource(Perscom $perscom): Batchable|Crudable
    {
        return $perscom->awardRecords();
    }

    public function getAward(): Award
    {
        return $this->award;
    }

    public function setAward(Award $award): void
    {
        $this->award = $award;
    }
}
