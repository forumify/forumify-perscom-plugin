<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\SortableEntityInterface;
use Forumify\Core\Entity\SortableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Perscom\Contracts\ResourceContract;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table('perscom_roster')]
class Roster implements PerscomEntityInterface, SortableEntityInterface
{
    use IdentifiableEntityTrait;
    use PerscomEntityTrait;
    use SortableEntityTrait;
    use TimestampableEntityTrait;

    #[Assert\NotBlank(allowNull: false)]
    #[ORM\Column]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description = '';

    #[ORM\ManyToMany(targetEntity: Unit::class)]
    #[ORM\JoinTable(
        name: 'perscom_roster_units',
        joinColumns: new JoinColumn(nullable: false, onDelete: 'CASCADE'),
        inverseJoinColumns: new JoinColumn(nullable: false, onDelete: 'CASCADE')
    )]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $units;

    public function __construct()
    {
        $this->units = new ArrayCollection();
    }

    public static function getPerscomResource(Perscom $perscom): ResourceContract
    {
        return $perscom->groups();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function setUnits(Collection $units): void
    {
        $this->units = $units;
    }
}
