<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;

#[AsEntityListener(Events::prePersist, 'prePersist', entity: AssignmentRecord::class)]
class AssignmentUpdateUserListener
{
    public function prePersist(AssignmentRecord $record): void
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
    }
}
