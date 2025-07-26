<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;

#[ORM\Entity]
#[ORM\Table('perscom_course_class_student')]
class CourseClassStudent
{
    use IdentifiableEntityTrait;

    /** @deprecated */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $perscomUserId = null;

    #[ORM\ManyToOne(targetEntity: PerscomUser::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?PerscomUser $user = null;

    #[ORM\ManyToOne(targetEntity: CourseClass::class, inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private CourseClass $class;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $result = null;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private ?array $qualifications = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $serviceRecordTextOverride = null;

    /** @deprecated */
    public function getPerscomUserId(): ?int
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'getUser()'));
        return $this->perscomUserId;
    }

    /** @deprecated */
    public function setPerscomUserId(int $perscomUserId): void
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'getUser()'));
        $this->perscomUserId = $perscomUserId;
    }

    public function getUser(): ?PerscomUser
    {
        return $this->user;
    }

    public function setUser(PerscomUser $user): void
    {
        $this->user = $user;
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
