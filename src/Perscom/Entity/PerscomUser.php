<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\Core\Entity\User;
use Forumify\Forum\Entity\Topic;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AwardRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\CombatRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\QualificationRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Perscom\Contracts\Batchable;
use Perscom\Contracts\Crudable;

#[ORM\Entity(repositoryClass: PerscomUserRepository::class)]
class PerscomUser implements PerscomEntityInterface
{
    use IdentifiableEntityTrait;
    use PerscomEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Rank::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Rank $rank = null;

    #[ORM\ManyToOne(targetEntity: Unit::class, inversedBy: 'users', fetch: 'EAGER')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(targetEntity: Position::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Position $position = null;

    #[ORM\ManyToOne(targetEntity: Specialty::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Specialty $specialty = null;

    #[ORM\ManyToOne(targetEntity: Status::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Status $status = null;

    #[ORM\Column(nullable: true)]
    private ?string $signature = null;

    #[ORM\Column(nullable: true)]
    private ?string $perscomSignature = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $signatureDirty = false;

    #[ORM\Column(nullable: true)]
    private ?string $uniform = null;

    #[ORM\Column(nullable: true)]
    private ?string $perscomUniform = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $uniformDirty = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ServiceRecord::class, fetch: 'EXTRA_LAZY')]
    private Collection $serviceRecords;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AwardRecord::class, fetch: 'EXTRA_LAZY')]
    private Collection $awardRecords;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CombatRecord::class, fetch: 'EXTRA_LAZY')]
    private Collection $combatRecords;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RankRecord::class, fetch: 'EXTRA_LAZY')]
    private Collection $rankRecords;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AssignmentRecord::class, fetch: 'EXTRA_LAZY')]
    private Collection $assignmentRecords;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: QualificationRecord::class, fetch: 'EXTRA_LAZY')]
    private Collection $qualificationRecords;

    #[ORM\OneToOne(targetEntity: Topic::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Topic $enlistmentTopic = null;

    public function __construct()
    {
        $this->serviceRecords = new ArrayCollection();
        $this->awardRecords = new ArrayCollection();
        $this->combatRecords = new ArrayCollection();
        $this->rankRecords = new ArrayCollection();
        $this->assignmentRecords = new ArrayCollection();
        $this->qualificationRecords = new ArrayCollection();
    }

    public static function getPerscomResource(Perscom $perscom): Batchable|Crudable
    {
        return $perscom->users();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRank(): ?Rank
    {
        return $this->rank;
    }

    public function setRank(?Rank $rank): void
    {
        $this->rank = $rank;
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

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): void
    {
        $this->status = $status;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): void
    {
        $this->signature = $signature;
    }

    public function getPerscomSignature(): ?string
    {
        return $this->perscomSignature;
    }

    public function setPerscomSignature(?string $perscomSignature): void
    {
        $this->perscomSignature = $perscomSignature;
    }

    public function isSignatureDirty(): bool
    {
        return $this->signatureDirty;
    }

    public function setSignatureDirty(bool $signatureDirty): void
    {
        $this->signatureDirty = $signatureDirty;
    }

    public function getUniform(): ?string
    {
        return $this->uniform;
    }

    public function setUniform(?string $uniform): void
    {
        $this->uniform = $uniform;
    }

    public function getPerscomUniform(): ?string
    {
        return $this->perscomUniform;
    }

    public function setPerscomUniform(?string $perscomUniform): void
    {
        $this->perscomUniform = $perscomUniform;
    }

    public function isUniformDirty(): bool
    {
        return $this->uniformDirty;
    }

    public function setUniformDirty(bool $uniformDirty): void
    {
        $this->uniformDirty = $uniformDirty;
    }

    public function getServiceRecords(): Collection
    {
        return $this->serviceRecords;
    }

    public function setServiceRecords(Collection $serviceRecords): void
    {
        $this->serviceRecords = $serviceRecords;
    }

    public function getAwardRecords(): Collection
    {
        return $this->awardRecords;
    }

    public function setAwardRecords(Collection $awardRecords): void
    {
        $this->awardRecords = $awardRecords;
    }

    public function getCombatRecords(): Collection
    {
        return $this->combatRecords;
    }

    public function setCombatRecords(Collection $combatRecords): void
    {
        $this->combatRecords = $combatRecords;
    }

    public function getRankRecords(): Collection
    {
        return $this->rankRecords;
    }

    public function setRankRecords(Collection $rankRecords): void
    {
        $this->rankRecords = $rankRecords;
    }

    public function getAssignmentRecords(): Collection
    {
        return $this->assignmentRecords;
    }

    public function setAssignmentRecords(Collection $assignmentRecords): void
    {
        $this->assignmentRecords = $assignmentRecords;
    }

    public function getQualificationRecords(): Collection
    {
        return $this->qualificationRecords;
    }

    public function setQualificationRecords(Collection $qualificationRecords): void
    {
        $this->qualificationRecords = $qualificationRecords;
    }

    public function getEnlistmentTopic(): ?Topic
    {
        return $this->enlistmentTopic;
    }

    public function setEnlistmentTopic(?Topic $enlistmentTopic): void
    {
        $this->enlistmentTopic = $enlistmentTopic;
    }
}
