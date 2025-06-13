<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Used as a foreign key towards the PERSCOM API.
 * Should not be used anywhere outside of sync logic.
 */
trait PerscomId
{
    #[ORM\Column(type: 'integer', unique: true, nullable: true)]
    private ?int $perscomId = null;

    public function getPerscomId(): ?int
    {
        return $this->perscomId;
    }

    public function setPerscomId(int $id): void
    {
        $this->perscomId = $id;
    }
}
