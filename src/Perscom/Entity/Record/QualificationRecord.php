<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Forumify\PerscomPlugin\Perscom\Repository\QualificationRecordRepository;

#[ORM\Entity(repositoryClass: QualificationRecordRepository::class)]
#[ORM\Table('perscom_record_qualification')]
class QualificationRecord implements RecordInterface
{
    use RecordFields;

    #[ORM\ManyToOne(targetEntity: Qualification::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Qualification $qualification;

    public function getQualification(): Qualification
    {
        return $this->qualification;
    }

    public function setQualification(Qualification $qualification): void
    {
        $this->qualification = $qualification;
    }
}
