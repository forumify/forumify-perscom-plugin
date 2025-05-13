<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use DateTime;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UserData
{
    #[Assert\NotBlank]
    private string $name;
    #[Assert\NotBlank]
    private string $email;
    private DateTime $createdAt;
    private ?int $status = null;
    private ?int $rank = null;
    private ?int $specialty = null;
    private ?int $position = null;
    private ?int $unit = null;
    #[Assert\Image(maxSize: '2048k')]
    private ?UploadedFile $uniform = null;
    #[Assert\Image(maxSize: '2048k')]
    private ?UploadedFile $signature = null;
    private array $secondaryAssignments = [];
    private array $customFields = [];

    public static function fromArray(array $user): self
    {
        $data = new self();

        $data->setName($user['name']);
        $data->setEmail($user['email']);
        $data->setRank($user['rank_id']);
        $data->setCreatedAt(new DateTime($user['created_at']));

        $data->setSpecialty($user['specialty_id']);
        $data->setStatus($user['status_id']);
        $data->setPosition($user['position_id']);
        $data->setUnit($user['unit_id']);

        $secondaryAssignments = array_map(
            fn (array $record): int => $record['id'],
            $user['secondary_assignment_records'] ?? []
        );
        $data->setSecondaryAssignments($secondaryAssignments);

        $customFields = [];
        foreach ($user['fields'] as $field) {
            $key = $field['key'];

            if (isset($user[$key])) {
                $customFields[$key] = $user[$key];
            }
        }
        $data->setCustomFields($customFields);

        return $data;
    }

    public function toUpdateArray(array $original): array
    {
        $new = [
            ...$this->getCustomFields(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'status_id' => $this->getStatus(),
            'rank_id' => $this->getRank(),
            'specialty_id' => $this->getSpecialty(),
            'position_id' => $this->getPosition(),
            'unit_id' => $this->getUnit(),
            'created_at' => $this->getCreatedAt()->format(Perscom::DATE_FORMAT),
        ];

        $updated = [];
        foreach ($new as $key => $value) {
            if (($original[$key] ?? null) !== $value) {
                $updated[$key] = $value;
            }
        }

        return $updated;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(?int $rank): void
    {
        $this->rank = $rank;
    }

    public function getSpecialty(): ?int
    {
        return $this->specialty;
    }

    public function setSpecialty(?int $specialty): void
    {
        $this->specialty = $specialty;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getUnit(): ?int
    {
        return $this->unit;
    }

    public function setUnit(?int $unit): void
    {
        $this->unit = $unit;
    }

    public function getUniform(): ?UploadedFile
    {
        return $this->uniform;
    }

    public function setUniform(?UploadedFile $uniform): void
    {
        $this->uniform = $uniform;
    }

    public function getSignature(): ?UploadedFile
    {
        return $this->signature;
    }

    public function setSignature(?UploadedFile $signature): void
    {
        $this->signature = $signature;
    }

    public function getSecondaryAssignments(): array
    {
        return $this->secondaryAssignments;
    }

    public function setSecondaryAssignments(array $secondaryAssignments): void
    {
        $this->secondaryAssignments = $secondaryAssignments;
    }

    public function getCustomFields(): array
    {
        return $this->customFields;
    }

    public function setCustomFields(array $customFields): void
    {
        $this->customFields = $customFields;
    }
}
