<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Entity\Document;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomId;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;

trait RecordFields
{
    use IdentifiableEntityTrait;
    use PerscomId;
    use TimestampableEntityTrait;

    #[ORM\ManyToOne(targetEntity: PerscomUser::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?PerscomUser $author = null;

    #[ORM\ManyToOne(targetEntity: PerscomUser::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PerscomUser $user;

    #[ORM\Column(type: 'text')]
    private string $text = '';

    #[ORM\ManyToOne(targetEntity: Document::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Document $document = null;

    public function getAuthor(): ?PerscomUser
    {
        return $this->author;
    }

    public function setAuthor(?PerscomUser $author): void
    {
        $this->author = $author;
    }

    public function getUser(): PerscomUser
    {
        return $this->user;
    }

    public function setUser(PerscomUser $user): void
    {
        $this->user = $user;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): void
    {
        $this->document = $document;
    }
}
