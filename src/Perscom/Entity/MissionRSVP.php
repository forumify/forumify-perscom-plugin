<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRSVPRepository;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: MissionRSVPRepository::class)]
#[ORM\Table('perscom_mission_rsvp')]
class MissionRSVP
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $perscomUserId;

    #[ORM\Id]
    #[ORM\ManyToOne(Mission::class, fetch: 'EXTRA_LAZY', inversedBy: 'rsvps')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Mission $mission;

    #[ORM\Column(type: 'boolean')]
    private bool $going = false;

    public function getPerscomUserId(): int
    {
        return $this->perscomUserId;
    }

    public function setPerscomUserId(int $perscomUserId): void
    {
        $this->perscomUserId = $perscomUserId;
    }

    public function getMission(): Mission
    {
        return $this->mission;
    }

    public function setMission(Mission $mission): void
    {
        $this->mission = $mission;
    }

    public function isGoing(): bool
    {
        return $this->going;
    }

    public function setGoing(bool $going): void
    {
        $this->going = $going;
    }
}
