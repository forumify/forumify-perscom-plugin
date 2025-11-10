<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\Entity\MissionRSVP;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Forumify\\Perscom\\MissionRoster', '@ForumifyPerscomPlugin/frontend/components/mission_roster.html.twig')]
class MissionRoster extends AbstractController
{

    public Mission $mission;

    private array $rsvpsByUserId = [];
    private ?array $unitsWithRSVPs = null;

    public function __construct(
        private readonly PerscomUserService $userService,
    ) {
    }

    public function getUnitsWithRSVPs(): array
    {
        if ($this->unitsWithRSVPs !== null) {
            return $this->unitsWithRSVPs;
        }

        $this->getRSVPsByUserId();
        $units = [];

        foreach ($this->rsvpsByUserId as $rsvp) {
            $user = $rsvp->getUser();
            $unit = $user?->getUnit();

            if ($unit && !isset($units[$unit->getId()])) {
                $units[$unit->getId()] = $unit;
            }
        }

        uasort($units, fn (Unit $a, Unit $b) => $a->getPosition() <=> $b->getPosition());
        $this->unitsWithRSVPs = $units;
        return $this->unitsWithRSVPs;
    }

    public function getUsersInUnitWithRSVPs(Unit $unit): array
    {
        $this->getRSVPsByUserId();
        $users = [];

        foreach ($unit->getUsers() as $user) {
            if (isset($this->rsvpsByUserId[$user->getId()])) {
                $users[$user->getId()] = $user;
            }
        }

        $this->userService->sortPerscomUsers($users);
        return $users;
    }

    public function getRSVPForUser(PerscomUser $user): ?MissionRSVP
    {
        $rsvps = $this->getRSVPsByUserId();
        return $rsvps[$user->getId()] ?? null;
    }

    private function getRSVPsByUserId(): array
    {
        if (!empty($this->rsvpsByUserId)) {
            return $this->rsvpsByUserId;
        }

        foreach ($this->mission->getRSVPs() as $rsvp) {
            if ($user = $rsvp->getUser()) {
                $this->rsvpsByUserId[$user->getId()] = $rsvp;
            }
        }

        return $this->rsvpsByUserId;
    }
}
