<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\SortableEntityInterface;
use Forumify\Core\Entity\SortableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\StatusRepository;
use Perscom\Contracts\Batchable;
use Perscom\Contracts\Crudable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
#[ORM\Table('perscom_status')]
class Status implements PerscomEntityInterface, SortableEntityInterface
{
    use IdentifiableEntityTrait;
    use PerscomEntityTrait;
    use SortableEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    private string $name;

    #[ORM\Column(length: 7, options: ['fixed' => true])]
    private string $color = '';

    public static function getPerscomResource(Perscom $perscom): Batchable|Crudable
    {
        return $perscom->statuses();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }
}
