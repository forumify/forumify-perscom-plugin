<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Repository\ReportInRepository;

#[ORM\Entity(repositoryClass: ReportInRepository::class)]
#[ORM\Table('perscom_report_in')]
class ReportIn
{
    use IdentifiableEntityTrait;

    /** @deprecated */
    #[ORM\Column(type: 'integer', nullable: true)]
    private int $perscomUserId;

    #[ORM\ManyToOne(targetEntity: PerscomUser::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?PerscomUser $user = null;

    #[ORM\Column(type: 'datetime')]
    private DateTime $lastReportInDate;

    /** @deprecated */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $previousStatusId = null;

    #[ORM\ManyToOne(targetEntity: Status::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Status $returnStatus = null;

    /** @deprecated */
    public function getPerscomUserId(): int
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
    }

    public function getLastReportInDate(): DateTime
    {
        return $this->lastReportInDate;
    }

    public function setLastReportInDate(DateTime $lastReportInDate): void
    {
        $this->lastReportInDate = $lastReportInDate;
    }

    /** @deprecated */
    public function getPreviousStatusId(): ?int
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'getReturnStatus()'));
        return $this->previousStatusId;
    }

    /** @deprecated */
    public function setPreviousStatusId(?int $previousStatusId): void
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'setReturnStatus()'));
        $this->previousStatusId = $previousStatusId;
    }

    public function getReturnStatus(): ?Status
    {
        return $this->returnStatus;
    }

    public function setReturnStatus(?Status $returnStatus): void
    {
        $this->returnStatus = $returnStatus;
    }
}
