<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomSyncResultRepository;

#[ORM\Entity(repositoryClass: PerscomSyncResultRepository::class)]
#[ORM\Table('perscom_sync_result')]
#[ORM\Index(fields: ['start'])]
class PerscomSyncResult
{
    use IdentifiableEntityTrait;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $start;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $end = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $success = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private string $errorMessage = '';

    public function __construct()
    {
        $this->start = new DateTimeImmutable();
    }

    public function getStart(): DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(DateTimeImmutable $start): void
    {
        $this->start = $start;
    }

    public function getEnd(): ?DateTimeImmutable
    {
        return $this->end;
    }

    public function setEnd(?DateTimeImmutable $end): void
    {
        $this->end = $end;
    }

    public function setEnded(): void
    {
        $this->end = new DateTimeImmutable();
    }

    public function isSuccess(): ?bool
    {
        return $this->success;
    }

    public function setSuccess(?bool $success): void
    {
        $this->success = $success;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    public function logMessage(string $message): void
    {
        $date = (new DateTime())->format('Y-m-d H:i:s.u');
        $this->errorMessage .= "[$date]: $message\n";
    }
}
