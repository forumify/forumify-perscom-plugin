<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\SortableEntityInterface;
use Forumify\Core\Entity\SortableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\SpecialtyRepository;
use Perscom\Contracts\ResourceContract;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SpecialtyRepository::class)]
#[ORM\Table('perscom_specialty')]
class Specialty implements PerscomEntityInterface, SortableEntityInterface
{
    use IdentifiableEntityTrait;
    use PerscomId;
    use SortableEntityTrait;
    use TimestampableEntityTrait;

    #[Assert\NotBlank(allowNull: false)]
    #[ORM\Column]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description = '';

    #[ORM\Column]
    private string $abbreviation = '';

    public static function getPerscomResource(Perscom $perscom): ResourceContract
    {
        return $perscom->specialties();
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

    public function getAbbreviation(): string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): void
    {
        $this->abbreviation = $abbreviation;
    }
}
