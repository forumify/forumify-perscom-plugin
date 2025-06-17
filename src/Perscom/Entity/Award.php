<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\SortableEntityInterface;
use Forumify\Core\Entity\SortableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Repository\AwardRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AwardRepository::class)]
#[ORM\Table('perscom_award')]
class Award implements PerscomEntityInterface, PerscomEntityWithImageInterface, SortableEntityInterface
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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $imageId = null;

    #[ORM\Column(nullable: true)]
    private ?string $image = null;

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

    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    public function setImageId(?int $imageId): void
    {
        $this->imageId = $imageId;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }
}
