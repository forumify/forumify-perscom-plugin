<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Forumify\PerscomPlugin\Perscom\Repository\ReportInRepository;

#[ORM\Entity(repositoryClass: ReportInRepository::class)]
#[ORM\Table('perscom_report_in')]
class ReportIn
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $perscomUserId;

    #[ORM\Column(type: 'datetime')]
    private DateTime $lastReportInDate;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $previousStatusId = null;

    public function getPerscomUserId(): int
    {
        return $this->perscomUserId;
    }

    public function setPerscomUserId(int $perscomUserId): void
    {
        $this->perscomUserId = $perscomUserId;
    }

    public function getLastReportInDate(): DateTime
    {
        return $this->lastReportInDate;
    }

    public function setLastReportInDate(DateTime $lastReportInDate): void
    {
        $this->lastReportInDate = $lastReportInDate;
    }

    public function getPreviousStatusId(): ?int
    {
        return $this->previousStatusId;
    }

    public function setPreviousStatusId(?int $previousStatusId): void
    {
        $this->previousStatusId = $previousStatusId;
    }
}
