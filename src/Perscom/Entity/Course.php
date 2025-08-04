<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\AccessControlledEntityInterface;
use Forumify\Core\Entity\ACLParameters;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\SluggableEntityTrait;
use Forumify\Core\Entity\SortableEntityInterface;
use Forumify\Core\Entity\SortableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Repository\CourseRepository;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ORM\Table(name: 'perscom_course')]
class Course implements AccessControlledEntityInterface, SortableEntityInterface
{
    use IdentifiableEntityTrait;
    use SluggableEntityTrait;
    use SortableEntityTrait;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $rankRequirement = null;

    #[ORM\ManyToOne(targetEntity: Rank::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Rank $minimumRank = null;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private array $prerequisites = [];

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private array $qualifications = [];

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: CourseClass::class, cascade: ['persist', 'remove'])]
    private Collection $classes;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: CourseInstructor::class, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $instructors;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getRankRequirement(): ?int
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'getMinimumRank()'));
        return $this->rankRequirement;
    }

    public function setRankRequirement(?int $rankRequirement): void
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'setMinimumRank()'));
        $this->rankRequirement = $rankRequirement;
    }

    public function getMinimumRank(): ?Rank
    {
        return $this->minimumRank;
    }

    public function setMinimumRank(?Rank $minimumRank): void
    {
        $this->minimumRank = $minimumRank;
        $this->rankRequirement = $minimumRank?->getPerscomId();
    }

    public function getPrerequisites(): array
    {
        return $this->prerequisites;
    }

    public function setPrerequisites(array $prerequisites): void
    {
        $this->prerequisites = $prerequisites;
    }

    /**
     * @return array<int>
     */
    public function getQualifications(): array
    {
        return array_map(fn (int $id) => $id, $this->qualifications);
    }

    public function setQualifications(array $qualifications): void
    {
        $this->qualifications = $qualifications;
    }

    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function setClasses(Collection $classes): void
    {
        $this->classes = $classes;
    }

    public function getInstructors(): Collection
    {
        return $this->instructors;
    }

    public function setInstructors(Collection $instructors): void
    {
        $this->instructors = $instructors;
    }

    public function getACLPermissions(): array
    {
        return [
            'view',
            'view_classes',
            'manage_classes',
            'signup_as_instructor',
        ];
    }

    public function getACLParameters(): ACLParameters
    {
        return new ACLParameters(
            self::class,
            (string)$this->getId(),
            'perscom_admin_courses_list',
            ['id' => $this->getId()],
        );
    }
}
