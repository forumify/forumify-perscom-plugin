<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\Core\Entity\TimestampableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Perscom\Contracts\ResourceContract;

#[ORM\Entity]
#[ORM\Table('perscom_form_submission')]
class FormSubmission implements PerscomEntityInterface
{
    use IdentifiableEntityTrait;
    use PerscomId;
    use TimestampableEntityTrait;

    #[ORM\ManyToOne(targetEntity: Form::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Form $form;

    #[ORM\ManyToOne(targetEntity: PerscomUser::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private PerscomUser $user;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $data = [];

    #[ORM\ManyToOne(targetEntity: Status::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Status $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $statusReason = null;

    public static function getPerscomResource(Perscom $perscom): ResourceContract
    {
        return $perscom->submissions();
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

    public function getUser(): PerscomUser
    {
        return $this->user;
    }

    public function setUser(PerscomUser $user): void
    {
        $this->user = $user;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): void
    {
        $this->status = $status;
    }

    public function getStatusReason(): ?string
    {
        return $this->statusReason;
    }

    public function setStatusReason(?string $statusReason): void
    {
        $this->statusReason = $statusReason;
    }
}
