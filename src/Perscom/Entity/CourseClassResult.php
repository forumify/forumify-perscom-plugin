<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassResultRepository;

#[ORM\Entity(repositoryClass: CourseClassResultRepository::class)]
#[ORM\Table('perscom_course_class_result')]
class CourseClassResult
{
    use IdentifiableEntityTrait;

    #[ORM\OneToOne(inversedBy: 'result', targetEntity: CourseClass::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private CourseClass $class;

    #[ORM\Column(type: 'json')]
    private array $result = [];

    public function getClass(): CourseClass
    {
        return $this->class;
    }

    public function setClass(CourseClass $class): void
    {
        $this->class = $class;
    }

    /**
     * @return array{
     *     instructors?: array<int, bool>,
     *     instructor_service_record?: bool,
     *     students?: array<int, array{result: string, qualifications: int[]}>,
     *     student_service_record?: bool,
     * }
     */
    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $result): void
    {
        $this->result = $result;
    }
}
