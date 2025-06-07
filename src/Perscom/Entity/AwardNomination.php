<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Repository\AwardNominationRepository;

#[ORM\Entity(repositoryClass: AwardNominationRepository::class)]
#[ORM\Table('award_nomination')]
class AwardNomination
{
    use IdentifiableEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $perscomUserId;

    #[ORM\Column(type: 'integer')]
    private int $receiverUserId;

    #[ORM\Column(type: 'integer')]
    private int $awardId;

    #[ORM\Column(type: 'text')]
    private string $reason;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdDate;

    #[ORM\Column(type: 'integer')]
    private int $status;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $updatedByUserId;

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getPerscomUserId(): int
    {
        return $this->perscomUserId;
    }

    public function setPerscomUserId(int $perscomUserId): void
    {
        $this->perscomUserId = $perscomUserId;
    }

    public function getReceiverUserId(): int
    {
        return $this->receiverUserId;
    }

    public function setReceiverUserId(int $receiverUserId): void
    {
        $this->receiverUserId = $receiverUserId;
    }

    public function getCreatedDate(): DateTime
    {
        return $this->createdDate;
    }

    public function setCreatedDate(DateTime $createdDate): void
    {
        $this->createdDate = $createdDate;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getAwardId(): int
    {
        return $this->awardId;
    }

    public function setAwardId(int $awardId): void
    {
        $this->awardId = $awardId;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    public function getUpdatedByUserId(): ?int
    {
        return $this->updatedByUserId;
    }

    public function setUpdatedByUserId(?int $id): void
    {
        $this->updatedByUserId = $id;
    }

    public static function fromArray(Array $arr): ?AwardNomination
    {
        if ($arr == null)
            return null;

        $nomination = new AwardNomination();
        $nomination->setId($arr['id']);
        $nomination->setReason($arr['reason']);
        $nomination->setAwardId($arr['awardId']);
        $nomination->setStatus($arr['status']);
        $nomination->setCreatedDate($arr['createdDate']);
        $nomination->setReceiverUserId($arr['receiverUserId']);
        $nomination->setPerscomUserId($arr['perscomUserId']);
        $nomination->setUpdatedByUserId($arr['updatedByUserId']);

        return $nomination;
    }
}
