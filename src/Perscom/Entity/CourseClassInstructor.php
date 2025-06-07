<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('perscom_course_class_instructor')]
class CourseClassInstructor
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $perscomUserId;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: CourseClass::class, inversedBy: 'instructors')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private CourseClass $class;

    #[ORM\ManyToOne(targetEntity: CourseInstructor::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?CourseInstructor $instructor = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $present = null;

    public function getPerscomUserId(): int
    {
        return $this->perscomUserId;
    }

    public function setPerscomUserId(int $perscomUserId): void
    {
        $this->perscomUserId = $perscomUserId;
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
