<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\Entity\MissionRSVP;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('Perscom\\MissionRSVPs', '@ForumifyPerscomPlugin/frontend/components/mission_rsvps.html.twig')]
class MissionRSVPs
{
    #[LiveProp]
    public Mission $mission;

    use DefaultActionTrait;

    public function __construct(
        private readonly PerscomUserRepository $perscomUserRepository,
    ) {
    }

    /**
     * @return array<int, array{ unit: Unit, rsvps: array<array{ rsvp: MissionRSVP, user: PerscomUser }>}>
     */
    public function getRSVPs(): array
    {
        $perscomUserIds = [];
        $rsvps = [];
        foreach ($this->mission->getRsvps() as $rsvp) {
            $id = $rsvp->getPerscomUserId();
            $perscomUserIds[] = $id;
            $rsvps[$id] = $rsvp;
        }

        $perscomUsers = $this->perscomUserRepository->findByPerscomIds($perscomUserIds);
        $allRsvps = [];
        foreach ($perscomUsers as $user) {
            $rsvp = $rsvps[$user->getPerscomId()] ?? null;
            if ($rsvp === null) {
                continue;
            }

            $allRsvps[] = [
                'rsvp' => $rsvp,
                'user' => $user,
            ];
        }

        return $this->groupByUnit($allRsvps);
    }

    /**
     * @param array<array{ rsvp: MissionRSVP, user: PerscomUser }> $rsvps
     * @return array<int, array{ unit: Unit, rsvps: array<array{ rsvp: MissionRSVP, user: PerscomUser }>}>
     */
    private function groupByUnit(array $rsvps): array
    {
        $units = [];
        foreach ($rsvps as $rsvp) {
            $user = $rsvp['user'];
            $unit = $user->getUnit();
            if ($unit === null) {
                continue;
            }

            $unitId = $unit->getPerscomId();
            if (!isset($units[$unitId])) {
                $units[$unitId]['unit'] = $unit;
            }

            $units[$unitId]['rsvps'][] = $rsvp;
        }

        uasort($units, fn (array $a, array $b): int => $a['unit']->getPosition() <=> $b['unit']->getPosition());
        return $units;
    }
}
