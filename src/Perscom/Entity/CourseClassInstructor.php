<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;

#[ORM\Entity]
#[ORM\Table('perscom_course_class_instructor')]
class CourseClassInstructor
{
    use IdentifiableEntityTrait;

    /** @deprecated */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $perscomUserId = null;

    #[ORM\ManyToOne(targetEntity: PerscomUser::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?PerscomUser $user = null;

    #[ORM\ManyToOne(targetEntity: CourseClass::class, inversedBy: 'instructors')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private CourseClass $class;

    #[ORM\ManyToOne(targetEntity: CourseInstructor::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?CourseInstructor $instructor = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $present = null;

    /** @deprecated */
    public function getPerscomUserId(): ?int
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'getUser()'));
        return $this->perscomUserId;
    }

    /** @deprecated */
    public function setPerscomUserId(int $perscomUserId): void
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'setUser()'));
        $this->perscomUserId = $perscomUserId;
    }

    public function getUser(): ?PerscomUser
    {
        return $this->user;
    }

    public function setUser(PerscomUser $user): void
    {
        $this->user = $user;
        $this->perscomUserId = $user->getPerscomId();
    }

    public function getClass(): CourseClass
    {
        return $this->class;
    }

    public function setClass(CourseClass $class): void
    {
        $this->class = $class;
    }

    public function getInstructor(): ?CourseInstructor
    {
        return $this->instructor;
    }

    public function setInstructor(?CourseInstructor $instructor): void
    {
        $this->instructor = $instructor;
    }

    public function isPresent(): ?bool
    {
        return $this->present;
    }

    public function setPresent(?bool $present): void
    {
        $this->present = $present;
    }
}
