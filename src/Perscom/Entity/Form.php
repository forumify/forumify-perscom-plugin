<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\FormRepository;
use Perscom\Contracts\Batchable;
use Perscom\Contracts\Crudable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FormRepository::class)]
#[ORM\Table('perscom_form')]
class Form implements PerscomEntityInterface
{
    use IdentifiableEntityTrait;
    use PerscomEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    private string $successMessage = '';

    #[ORM\ManyToOne(targetEntity: Status::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Status $defaultStatus;

    #[ORM\Column(type: Types::TEXT)]
    private string $description = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $instructions = '';

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: FormSubmission::class)]
    private Collection $submissions;

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: FormField::class, cascade: ['persist', 'remove'])]
    private Collection $fields;

    public function __construct()
    {
        $this->submissions = new ArrayCollection();
        $this->fields = new ArrayCollection();
    }

    public static function getPerscomResource(Perscom $perscom): Batchable|Crudable
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

    public function setSubmissions(Collection $submissions): void
    {
        $this->submissions = $submissions;
    }

    public function addSubmission(FormSubmission $submission): void
    {
        $this->submissions->add($submission);
    }

    public function removeSubmission(FormSubmission $submission): void
    {
        $this->submissions->removeElement($submission);
    }

    /**
     * @return Collection<int, FormField>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function setFields(Collection $fields): void
    {
        $this->fields = $fields;
    }

    public function addField(FormField $field): void
    {
        $this->fields->add($field);
    }

    public function removeField(FormField $field): void
    {
        $this->fields->removeElement($field);
    }
}
