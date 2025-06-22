<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Perscom\Contracts\ResourceContract;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table('perscom_form')]
class Form implements PerscomEntityInterface
{
    use IdentifiableEntityTrait;
    use PerscomId;
    use TimestampableEntityTrait;

    #[Assert\NotBlank(allowNull: false)]
    #[ORM\Column]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    private string $successMessage;

    #[ORM\ManyToOne(targetEntity: Status::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Status $defaultStatus;

    #[ORM\Column(type: Types::TEXT)]
    private string $description = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $instructions = '';

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $fields = [];

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: FormSubmission::class)]
    private Collection $submissions;

    public static function getPerscomResource(Perscom $perscom): ResourceContract
    {
        return $perscom->forms();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSuccessMessage(): string
    {
        return $this->successMessage;
    }

    public function setSuccessMessage(string $successMessage): void
    {
        $this->successMessage = $successMessage;
    }

    public function getDefaultStatus(): ?Status
    {
        return $this->defaultStatus;
    }

    public function setDefaultStatus(?Status $defaultStatus): void
    {
        $this->defaultStatus = $defaultStatus;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getInstructions(): string
    {
        return $this->instructions;
    }

    public function setInstructions(string $instructions): void
    {
        $this->instructions = $instructions;
    }

    public function getSubmissions(): Collection
    {
        return $this->submissions;
    }

    public function getFields(): array
    {
        return $this->fields ?? [];
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function setSubmissions(Collection $submissions): void
    {
        $this->submissions = $submissions;
    }
}
