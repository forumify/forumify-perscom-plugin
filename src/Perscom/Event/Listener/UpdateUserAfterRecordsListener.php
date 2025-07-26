<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event\Listener;

use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;
use Forumify\PerscomPlugin\Perscom\Event\RecordsCreatedEvent;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class UpdateUserAfterRecordsListener
{
    public function __construct(private readonly PerscomUserRepository $userRepository)
    {
    }

    public function __invoke(RecordsCreatedEvent $recordsEvent)
    {
        foreach ($recordsEvent->records as $record) {
            if ($record instanceof AssignmentRecord) {
                $this->handleAssignment($record);
            } elseif ($record instanceof RankRecord) {
                $this->handleRank($record);
            }
        }
    }

    private function handleAssignment(AssignmentRecord $record): void
    {
        if ($record->getType() !== 'primary') {
            return;
        }

        $user = $record->getUser();
        if ($status = $record->getStatus()) {
            $user->setStatus($status);
        }

        if ($unit = $record->getUnit()) {
            $user->setUnit($unit);
        }

        if ($position = $record->getPosition()) {
            $user->setPosition($position);
        }

        if ($specialty = $record->getSpecialty()) {
            $user->setSpecialty($specialty);
        }

        $this->userRepository->save($user);
    }

    private function handleRank(RankRecord $record): void
    {
        $user = $record->getUser();
        $user->setRank($record->getRank());

        $this->userRepository->save($user);
    }
}
