<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Roster;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Forumify\PerscomPlugin\Perscom\Repository\AssignmentRecordRepository;
use Forumify\PerscomPlugin\Perscom\Repository\RosterRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('Forumify\\Perscom\\Roster', '@ForumifyPerscomPlugin/frontend/components/roster.html.twig')]
class RosterView extends AbstractController
{
    use DefaultActionTrait;

    /** @var array<Roster> */
    public array $rosters;
    #[LiveProp(writable: true)]
    public ?int $selectedRosterId = null;

    public function __construct(
        private readonly RosterRepository $rosterRepository,
        private readonly AssignmentRecordRepository $assignmentRecordRepository,
        private readonly PerscomUserService $userService,
    ) {
    }

    public function getRosters(): array
    {
        $this->rosters = $this->rosterRepository->findAll();
        if ($this->selectedRosterId === null) {
            $first = reset($this->rosters);
            if ($first) {
                $this->selectedRosterId = $first->getId();
            }
        }
        return $this->rosters;
    }

    #[LiveAction]
    public function selectRoster(#[LiveArg] int $rosterId): void
    {
        $this->selectedRosterId = $rosterId;
    }

    public function getRoster(): ?Roster
    {
        foreach ($this->rosters as $r) {
            if ($r->getId() === $this->selectedRosterId) {
                return $r;
            }
        }
        return null;
    }

    public function getUsersInUnit(Unit $unit): array
    {
        $allUsers = [];

        $primaryAssigned = $unit->getUsers()->toArray();
        foreach ($primaryAssigned as $primary) {
            $allUsers[$primary->getId()] = $primary;
        }

        $secondaryAssigned = $this->assignmentRecordRepository
            ->createQueryBuilder('ar')
            ->select('ar')
            ->join('ar.user', 'u')
            ->where('ar.type = :type')
            ->andWhere('ar.unit = :unit')
            ->setParameter('type', 'secondary')
            ->setParameter('unit', $unit)
            ->getQuery()
            ->getResult()
        ;

        /** @var AssignmentRecord $secondary */
        foreach ($secondaryAssigned as $secondary) {
            $user = $secondary->getUser();
            $allUsers[$user->getId()] = $user;
            $user->setPosition($secondary->getPosition());
            $user->setSpecialty($secondary->getSpecialty());
        }

        $this->userService->sortPerscomUsers($allUsers);
        return $allUsers;
    }
}
