<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('perscom_course_class_student')]
class CourseClassStudent
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $perscomUserId;
    private ?array $perscomUser = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: CourseClass::class, inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private CourseClass $class;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $result = null;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private ?array $qualifications = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $serviceRecordTextOverride = null;

    public function getPerscomUserId(): int
    {
        return $this->perscomUserId;
    }

    public function setPerscomUserId(int $perscomUserId): void
    {
        $this->perscomUserId = $perscomUserId;
    }

    public function getPerscomUser(): ?array
    {
        return $this->perscomUser;
    }

    public function setPerscomUser(?array $perscomUser): void
    {
        $this->perscomUser = $perscomUser;
    }

    public function getClass(): CourseClass
    {
        return $this->class;
    }

    public function setClass(CourseClass $class): void
    {
        $this->class = $class;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): void
    {
        $this->result = $result;
    }

    /**
     * @return array<int>
     */
    public function getQualifications(): array
    {
        return $this->qualifications === null
            ? []
            : array_map(fn ($qual) => (int)$qual, $this->qualifications);
    }

    public function setQualifications(?array $qualifications): void
    {
        $this->qualifications = $qualifications;
    }

    public function getServiceRecordTextOverride(): ?string
    {
        return $this->serviceRecordTextOverride;
    }

    public function setServiceRecordTextOverride(?string $serviceRecordTextOverride): void
    {
        $this->serviceRecordTextOverride = $serviceRecordTextOverride;
    }
}
