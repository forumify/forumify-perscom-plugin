<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\User;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;

#[ORM\Entity(repositoryClass: PerscomUserRepository::class)]
class PerscomUser
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    private int $id;

    #[ORM\OneToOne(targetEntity: User::class)]
    private User $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}

