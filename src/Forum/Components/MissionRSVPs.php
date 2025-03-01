<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Perscom\Data\FilterObject;
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
        private readonly PerscomUserService $perscomUserService,
        private readonly PerscomFactory $perscomFactory,
    ) {
    }

    public function getRSVPs(): array
    {
        $perscomUserIds = [];
        $rsvps = [];
        foreach ($this->mission->getRsvps() as $rsvp) {
            $id = $rsvp->getPerscomUserId();
            $perscomUserIds[] = $id;
            $rsvps[$id] = $rsvp;
        }

        try {
            $perscomUsers = $this->perscomFactory
                ->getPerscom()
                ->users()
                ->search(
                    filter: new FilterObject('id', 'in', $perscomUserIds),
                    include: ['unit', 'position', 'rank', 'rank.image'],
                )
                ->json('data')
            ;
        } catch (\Exception) {
            return [];
        }

        foreach ($perscomUsers as $i => $user) {
            $rsvp = $rsvps[$user['id']] ?? null;
            if ($rsvp === null) {
                continue;
            }
            $perscomUsers[$i]['rsvp'] = $rsvp;
        }

        return $this->groupByUnit($perscomUsers);
    }

    private function groupByUnit(array $users): array
    {
        $units = [];
        foreach ($users as $user) {
            $unitId = $user['unit_id'] ?? null;
            if ($unitId === null) {
                continue;
            }

            if (!isset($units[$unitId])) {
                $units[$unitId] = $user['unit'];
            }

            $units[$unitId]['users'][] = $user;
        }

        foreach ($units as &$unit) {
            $this->perscomUserService->sortUsers($unit['users']);
        }
        unset($unit);

        uasort($units, fn (array $a, array $b): int => $a['position'] <=> $b['position']);
        return $units;
    }
}
