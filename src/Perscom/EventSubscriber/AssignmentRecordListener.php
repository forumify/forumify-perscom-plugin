<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;

#[AsEntityListener(Events::postPersist, 'postPersist', entity: AssignmentRecord::class)]
class AssignmentRecordListener
{
    public function __construct(private readonly PerscomUserRepository $userRepository)
    {
    }

    public function postPersist(AssignmentRecord $assignmentRecord): void
    {
        if ($assignmentRecord->getType() !== 'primary') {
            return;
        }

        $user = $assignmentRecord->getUser();
        if ($status = $assignmentRecord->getStatus()) {
            $user->setStatus($status);
        }

        if ($unit = $assignmentRecord->getUnit()) {
            $user->setUnit($unit);
        }

        if ($position = $assignmentRecord->getPosition()) {
            $user->setPosition($position);
        }

        if ($specialty = $assignmentRecord->getSpecialty()) {
            $user->setSpecialty($specialty);
        }

        $this->userRepository->save($user);
    }
}
