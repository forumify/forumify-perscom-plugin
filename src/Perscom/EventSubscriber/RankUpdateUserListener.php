<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;

#[AsEntityListener(Events::prePersist, 'prePersist', entity: RankRecord::class)]
class RankUpdateUserListener
{
    public function prePersist(RankRecord $record): void
    {
        $record->getUser()->setRank($record->getRank());
    }
}
