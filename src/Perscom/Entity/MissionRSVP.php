<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\IdentifiableEntityTrait;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRSVPRepository;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: MissionRSVPRepository::class)]
#[ORM\Table('perscom_mission_rsvp')]
class MissionRSVP
{
    use IdentifiableEntityTrait;
    use TimestampableEntity;

    /** @deprecated */
    #[ORM\Column(type: 'integer')]
    private int $perscomUserId;

    #[ORM\ManyToOne(targetEntity: PerscomUser::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?PerscomUser $user = null;

    #[ORM\ManyToOne(Mission::class, fetch: 'EXTRA_LAZY', inversedBy: 'rsvps')]
    #[ORM\JoinColumn(onDelete: 'CASCADE', nullable: false)]
    private Mission $mission;

    #[ORM\Column(type: 'boolean')]
    private bool $going = false;

    /** @deprecated */
    public function getPerscomUserId(): int
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'getUser()'));
        return $this->perscomUserId;
    }

    /** @deprecated */
    public function setPerscomUserId(int $perscomUserId): void
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.0', sprintf('%s is deprecated. Use %s instead', __CLASS__ . '::' . __FUNCTION__, __CLASS__ . '::' . 'setUser()'));
        $this->perscomUserId = $perscomUserId;
    }

    public function getUser(): ?PerscomUser
    {
        return $this->user;
    }

    public function setUser(PerscomUser $user): void
    {
        $this->user = $user;
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
