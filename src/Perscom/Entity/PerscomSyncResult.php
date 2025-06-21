<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;

#[ORM\Entity]
#[ORM\Table('perscom_sync_result')]
class PerscomSyncResult
{
    use IdentifiableEntityTrait;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $start;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $end;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $success = true;

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

    public function getEnd(): DateTimeImmutable
    {
        return $this->end;
    }

    public function setEnded(): void
    {
        $this->end = new DateTimeImmutable();
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): void
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
        $this->errorMessage .= "\n\n$message";
    }
}
