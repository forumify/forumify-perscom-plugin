<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Forumify\PerscomPlugin\Perscom\Entity\Specialty;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\AssignmentRecordRepository;
use Perscom\Contracts\Batchable;
use Perscom\Contracts\Crudable;

#[ORM\Entity(repositoryClass: AssignmentRecordRepository::class)]
#[ORM\Index(fields: ['type'])]
#[ORM\Table('perscom_record_assignment')]
class AssignmentRecord implements RecordInterface
{
    use RecordFields;

    #[Column(length: 16)]
    private string $type = 'primary';

    #[ORM\ManyToOne(targetEntity: Status::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Status $status = null;

    #[ORM\ManyToOne(targetEntity: Unit::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(targetEntity: Position::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Position $position = null;

    #[ORM\ManyToOne(targetEntity: Specialty::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Specialty $specialty = null;

    #[ORM\ManyToOne(targetEntity: PerscomUser::class, inversedBy: 'assignmentRecords', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PerscomUser $user;

    public static function getPerscomResource(Perscom $perscom): Batchable|Crudable
    {
        return $perscom->assignmentRecords();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): void
    {
        $this->status = $status;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): void
    {
        $this->unit = $unit;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): void
    {
        $this->position = $position;
    }

    public function getSpecialty(): ?Specialty
    {
        return $this->specialty;
    }

    public function setSpecialty(?Specialty $specialty): void
    {
        $this->specialty = $specialty;
    }
}
