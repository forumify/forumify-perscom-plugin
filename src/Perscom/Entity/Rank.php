<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\SortableEntityInterface;
use Forumify\Core\Entity\SortableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\RankRepository;
use Perscom\Contracts\Batchable;
use Perscom\Contracts\Crudable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RankRepository::class)]
#[ORM\Table('perscom_rank')]
class Rank implements PerscomEntityInterface, PerscomEntityWithImageInterface, SortableEntityInterface
{
    use IdentifiableEntityTrait;
    use PerscomEntityTrait;
    use PerscomImageTrait;
    use SortableEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description = '';

    #[ORM\Column(length: 8)]
    #[Assert\Length(max: 8)]
    private string $abbreviation = '';

    #[ORM\Column(length: 16)]
    #[Assert\Length(max: 16)]
    private string $paygrade = '';

    public function getImageEndpoint(): string
    {
        return "ranks/{$this->getPerscomId()}/image";
    }

    public static function getPerscomResource(Perscom $perscom): Batchable|Crudable
    {
        return $perscom->ranks();
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

    public function getPaygrade(): string
    {
        return $this->paygrade;
    }

    public function setPaygrade(string $paygrade): void
    {
        $this->paygrade = $paygrade;
    }
}
