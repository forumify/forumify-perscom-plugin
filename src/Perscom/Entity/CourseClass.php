<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\BlameableEntityTrait;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassRepository;

#[ORM\Entity(repositoryClass: CourseClassRepository::class)]
#[ORM\Table('perscom_course_class')]
class CourseClass
{
    use IdentifiableEntityTrait;
    use BlameableEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'classes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Course $course;

    #[ORM\Column]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'datetime')]
    private DateTime $signupFrom;

    #[ORM\Column(type: 'datetime')]
    private DateTime $signupUntil;

    #[ORM\Column(type: 'datetime')]
    private DateTime $start;

    #[ORM\Column(type: 'datetime')]
    private DateTime $end;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: 'simple_array', nullable: true)]
    private array $instructors = [];

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $instructorSlots = null;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: 'simple_array', nullable: true)]
    private array $students = [];

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $studentSlots = null;

    #[ORM\OneToOne(mappedBy: 'class', targetEntity: CourseClassResult::class)]
    private ?CourseClassResult $result = null;

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

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

    public function getSignupFrom(): DateTime
    {
        return $this->signupFrom;
    }

    public function setSignupFrom(DateTime $signupFrom): void
    {
        $this->signupFrom = $signupFrom;
    }

    public function getSignupUntil(): DateTime
    {
        return $this->signupUntil;
    }

    public function setSignupUntil(DateTime $signupUntil): void
    {
        $this->signupUntil = $signupUntil;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function setStart(DateTime $start): void
    {
        $this->start = $start;
    }

    public function getEnd(): DateTime
    {
        return $this->end;
    }

    public function setEnd(DateTime $end): void
    {
        $this->end = $end;
    }

    /**
     * @return array<int>
     */
    public function getInstructors(): array
    {
        return array_map(static fn (int $id) => $id, $this->instructors);
    }

    /**
     * @param array<int|string> $instructors
     */
    public function setInstructors(array $instructors): void
    {
        $this->instructors = $instructors;
    }

    public function addInstructor(int $instructorId): void
    {
        $this->instructors[] = $instructorId;
    }

    public function getInstructorSlots(): ?int
    {
        return $this->instructorSlots;
    }

    public function setInstructorSlots(?int $instructorSlots): void
    {
        $this->instructorSlots = $instructorSlots;
    }

    /**
     * @return array<int>
     */
    public function getStudents(): array
    {
        return array_map(static fn (int $id) => $id, $this->students);
    }

    /**
     * @param array<int|string> $students
     */
    public function setStudents(array $students): void
    {
        $this->students = $students;
    }

    public function addStudent(int $studentId): void
    {
        $this->students[] = $studentId;
    }

    public function getStudentSlots(): ?int
    {
        return $this->studentSlots;
    }

    public function setStudentSlots(?int $studentSlots): void
    {
        $this->studentSlots = $studentSlots;
    }

    public function getResult(): ?CourseClassResult
    {
        return $this->result;
    }

    public function setResult(?CourseClassResult $result): void
    {
        $this->result = $result;
    }
}
