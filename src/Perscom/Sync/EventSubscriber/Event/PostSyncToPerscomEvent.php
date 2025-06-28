<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\EventSubscriber\Event;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomEntityInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PostSyncToPerscomEvent extends Event
{
    /**
     * @param array{
     *      create: PerscomEntityInterface[],
     *      update: PerscomEntityInterface[],
     *      delete: array<class-string<PerscomEntityInterface>, int[]>,
     *  } $changeSet
     */
    public function __construct(
        public readonly array $changeSet,
    ) {
    }
}
