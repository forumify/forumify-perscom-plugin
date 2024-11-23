<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\BlameableEntityTrait;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Repository\AfterActionReportRepository;

#[ORM\Entity(repositoryClass: AfterActionReportRepository::class)]
#[ORM\Table('perscom_after_action_report')]
#[ORM\UniqueConstraint(name: 'unit_mission_uniq', fields: ['mission', 'unitId'])]
class AfterActionReport
{
    use IdentifiableEntityTrait;
    use BlameableEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\Column(type: 'integer')]
    private int $unitId;

    #[ORM\Column]
    private string $unitName;

    #[ORM\Column(type: 'integer', options: ['default' => 100])]
    private int $unitPosition;

    #[ORM\Column(type: 'text')]
    private string $report;

    #[ORM\Column(type: 'json')]
    private array $attendance;

    #[ORM\ManyToOne(targetEntity: Mission::class, fetch: 'EXTRA_LAZY', inversedBy: 'afterActionReports')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Mission $mission;

    public function getUnitId(): int
    {
        return $this->unitId;
    }

    public function setUnitId(int $unitId): void
    {
        $this->unitId = $unitId;
    }

    public function getUnitName(): string
    {
        return $this->unitName;
    }

    public function setUnitName(string $unitName): void
    {
        $this->unitName = $unitName;
    }

    public function getUnitPosition(): int
    {
        return $this->unitPosition;
    }

    public function setUnitPosition(int $unitPosition): void
    {
        $this->unitPosition = $unitPosition;
    }

    public function getReport(): string
    {
        return $this->report;
    }

    public function setReport(string $report): void
    {
        $this->report = $report;
    }

    public function getAttendance(): array
    {
        return $this->attendance;
    }

    public function setAttendance(array $attendance): void
    {
        $this->attendance = $attendance;
    }

    public function getMission(): Mission
    {
        return $this->mission;
    }

    public function setMission(Mission $mission): void
    {
        $this->mission = $mission;
    }
}
