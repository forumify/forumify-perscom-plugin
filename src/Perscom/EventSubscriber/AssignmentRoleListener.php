<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;

#[AsEntityListener(Events::prePersist, 'prePersist', entity: AssignmentRecord::class, priority: 100)]
#[AsEntityListener(Events::preRemove, 'preRemove', entity: AssignmentRecord::class)]
class AssignmentRoleListener
{
    public function prePersist(AssignmentRecord $addedRecord): void
    {
        // TODO
    }

    public function preRemove(AssignmentRecord $deletedRecord): void
    {
        // TODO
    }
}
