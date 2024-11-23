<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\BlameableEntityTrait;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\SluggableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRepository;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
#[ORM\Table('perscom_mission')]
class Mission
{
    use IdentifiableEntityTrait;
    use SluggableEntityTrait;
    use BlameableEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\Column]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $briefing;

    #[ORM\Column(type: 'datetime')]
    private DateTime $start;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $end;

    #[ORM\ManyToOne(targetEntity: Operation::class, fetch: 'EXTRA_LAZY', inversedBy: 'missions')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Operation $operation;

    #[ORM\OneToMany(mappedBy: 'mission', targetEntity: AfterActionReport::class, cascade: ['persist', 'remove'])]
    private Collection $afterActionReports;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $sendNotification;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $createCombatRecords;

    #[ORM\Column(nullable: true)]
    private ?string $combatRecordText = null;

    public function __construct()
    {
        $this->afterActionReports = new ArrayCollection();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getBriefing(): string
    {
        return $this->briefing;
    }

    public function setBriefing(string $briefing): void
    {
        $this->briefing = $briefing;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function setStart(DateTime $start): void
    {
        $this->start = $start;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function setEnd(?DateTime $end): void
    {
        $this->end = $end;
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function setOperation(Operation $operation): void
    {
        $this->operation = $operation;
    }

    /**
     * @return Collection<AfterActionReport>
     */
    public function getAfterActionReports(): Collection
    {
        return $this->afterActionReports;
    }

    public function setAfterActionReports(Collection $afterActionReports): void
    {
        $this->afterActionReports = $afterActionReports;
    }

    public function isSendNotification(): bool
    {
        return $this->sendNotification;
    }

    public function setSendNotification(bool $sendNotification): void
    {
        $this->sendNotification = $sendNotification;
    }

    public function isCreateCombatRecords(): bool
    {
        return $this->createCombatRecords;
    }

    public function setCreateCombatRecords(bool $createCombatRecords): void
    {
        $this->createCombatRecords = $createCombatRecords;
    }

    public function getCombatRecordText(): ?string
    {
        return $this->combatRecordText;
    }

    public function setCombatRecordText(?string $combatRecordText): void
    {
        $this->combatRecordText = $combatRecordText;
    }
}
