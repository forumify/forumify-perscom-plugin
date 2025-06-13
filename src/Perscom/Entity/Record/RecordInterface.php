<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use DateTime;
use Forumify\PerscomPlugin\Perscom\Entity\Document;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;

interface RecordInterface
{
    public function getPerscomId(): ?int;
    public function setPerscomId(int $id): void;
    public function getAuthor(): ?PerscomUser;
    public function setAuthor(?PerscomUser $author): void;
    public function getUser(): PerscomUser;
    public function setUser(PerscomUser $user): void;
    public function getText(): string;
    public function setText(string $text): void;
    public function getDocument(): ?Document;
    public function setDocument(?Document $document): void;
    public function getCreatedAt(): DateTime;
    public function setCreatedAt(DateTime $createdAt): void;
    public function getUpdatedAt(): ?DateTime;
    public function setUpdatedAt(?DateTime $updatedAt): void;
}
