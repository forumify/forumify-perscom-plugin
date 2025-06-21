<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\QualificationRecordRepository;
use Perscom\Contracts\ResourceContract;

#[ORM\Entity(repositoryClass: QualificationRecordRepository::class)]
#[ORM\Table('perscom_record_qualification')]
class QualificationRecord implements RecordInterface
{
    use RecordFields;

    #[ORM\ManyToOne(targetEntity: Qualification::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Qualification $qualification;

    public static function getPerscomResource(Perscom $perscom): ResourceContract
    {
        return $perscom->qualificationRecords();
    }

    public function getQualification(): Qualification
    {
        return $this->qualification;
    }

    public function setQualification(Qualification $qualification): void
    {
        $this->qualification = $qualification;
    }
}
