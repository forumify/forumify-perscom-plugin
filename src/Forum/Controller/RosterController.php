<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RosterController extends AbstractController
{
    #[Route('/roster', 'roster')]
    public function __invoke(PerscomFactory $perscomFactory): Response
    {
        $perscom = $perscomFactory->getPerscom();
        $groups = $perscom
            ->groups()
            ->all([
                'units',
                'units.users',
                'units.users.position',
                'units.users.rank',
                'units.users.rank.image',
                'units.users.status',
                'units.secondary_assignment_records',
                'units.secondary_assignment_records.position',
                'units.secondary_assignment_records.user',
                'units.secondary_assignment_records.user.rank',
                'units.secondary_assignment_records.user.rank.image',
                'units.secondary_assignment_records.user.status',
            ])
            ->json('data') ?? [];

        return $this->render('@ForumifyPerscomPlugin/frontend/roster/roster.html.twig', [
            'groups' => array_map($this->mergeSecondaryUnitsIntoPrimary(...), $groups),
        ]);
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
