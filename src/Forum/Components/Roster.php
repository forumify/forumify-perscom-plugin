<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\ScopeObject;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('Forumify\Perscom\Roster', '@ForumifyPerscomPlugin/frontend/components/roster.html.twig')]
class Roster
{
    use DefaultActionTrait;

    #[LiveProp]
    public array $groups = [];

    #[LiveProp(writable: true)]
    public ?int $selectedGroup = null;

    public function __construct(private readonly PerscomFactory $perscomFactory, private Stopwatch $stopwatch)
    {
    }

    #[LiveAction]
    public function selectGroup(#[LiveArg] int $groupId): void
    {
        $this->selectedGroup = $groupId;
    }

    public function getGroup(): array
    {
        $groupId = $this->selectedGroup ?? ($this->groups[0]['id'] ?? null);
        if ($groupId === null) {
            return [];
        }

        $group = $this->perscomFactory
            ->getPerscom()
            ->roster()
            ->group(
                id: $groupId,
                include: [
                    'units.secondary_assignment_records',
                    'units.secondary_assignment_records.position',
                    'units.secondary_assignment_records.user',
                    'units.secondary_assignment_records.user.rank',
                    'units.secondary_assignment_records.user.rank.image',
                    'units.secondary_assignment_records.user.status',
                ]
            )
            ->json('data') ?? [];

        return $this->mergeSecondaryUnitsIntoPrimary($group);
    }

    private function mergeSecondaryUnitsIntoPrimary(array $group): array
    {
        foreach ($group['units'] as &$unit) {
            foreach ($unit['secondary_assignment_records'] ?? [] as $secondaryAssignment) {
                if (empty($secondaryAssignment['user'])) {
                    continue;
                }

                $secondaryAssignment['user']['position'] = $secondaryAssignment['position'] ?? ['name' => ''];
                $unit['users'][] = $secondaryAssignment['user'];
            }
        }

        return $group;
    }
}
