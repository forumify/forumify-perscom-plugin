<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\Core\Entity\User;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;

#[ORM\Entity(repositoryClass: PerscomUserRepository::class)]
class PerscomUser implements PerscomEntityInterface
{
    use IdentifiableEntityTrait;
    use PerscomId;
    use TimestampableEntityTrait;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column]
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

    #[ORM\ManyToMany(targetEntity: Position::class, fetch: 'EAGER')]
    #[ORM\JoinTable(
        name: 'perscom_user_secondary_positions',
        joinColumns: new JoinColumn(nullable: false, onDelete: 'CASCADE'),
        inverseJoinColumns: new JoinColumn(nullable: false, onDelete: 'CASCADE')
    )]
    private Collection $secondaryPositions;

    #[ORM\ManyToOne(targetEntity: Status::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Status $status = null;

    #[ORM\Column(nullable: true)]
    private ?string $signature = null;

    #[ORM\Column(nullable: true)]
    private ?string $perscomSignature = null;

    #[ORM\Column(nullable: true)]
    private ?string $uniform = null;

    #[ORM\Column(nullable: true)]
    private ?string $perscomUniform = null;

    public function __construct()
    {
        $this->secondaryPositions = new ArrayCollection();
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

    public function getSecondaryPositions(): Collection
    {
        return $this->secondaryPositions;
    }

    public function setSecondaryPositions(Collection $secondaryPositions): void
    {
        $this->secondaryPositions = $secondaryPositions;
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
}
