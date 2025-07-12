<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\SortableEntityInterface;
use Forumify\Core\Entity\SortableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\PositionRepository;
use Perscom\Contracts\ResourceContract;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PositionRepository::class)]
#[ORM\Table('perscom_position')]
class Position implements PerscomEntityInterface, SortableEntityInterface
{
    use IdentifiableEntityTrait;
    use PerscomEntityTrait;
    use SortableEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description = '';

    public static function getPerscomResource(Perscom $perscom): ResourceContract
    {
        return $perscom->positions();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
