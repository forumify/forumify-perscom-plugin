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
class AfterActionReport
{
    use IdentifiableEntityTrait;
    use BlameableEntityTrait;
    use TimestampableEntityTrait;

    /** @deprecated */
    #[ORM\Column(type: 'integer', nullable: true)]
    private int $perscomUnitId;

    #[ORM\ManyToOne(targetEntity: Unit::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Unit $unit = null;

    #[ORM\Column(type: 'text')]
    private string $report;

    #[ORM\Column(type: 'json')]
    private array $attendance;

    #[ORM\ManyToOne(targetEntity: Mission::class, fetch: 'EXTRA_LAZY', inversedBy: 'afterActionReports')]
    #[ORM\JoinColumn(onDelete: 'CASCADE', nullable: false)]
    private Mission $mission;

    /** @deprecated */
    public function getUnitId(): int
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'getUnit()'));
        return $this->perscomUnitId;
    }

    /** @deprecated */
    public function setUnitId(int $unitId): void
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'setUnit()'));
        $this->perscomUnitId = $unitId;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(Unit $unit): void
    {
        $this->unit = $unit;
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
