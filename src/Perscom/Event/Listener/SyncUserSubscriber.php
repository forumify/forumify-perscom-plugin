<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event\Listener;

use Forumify\PerscomPlugin\Perscom\Event\RecordsCreatedEvent;
use Forumify\PerscomPlugin\Perscom\Service\SyncUserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SyncUserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SyncUserService $syncUserService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RecordsCreatedEvent::class => 'onRecordsCreated'
        ];
    }

    public function onRecordsCreated(RecordsCreatedEvent $event): void
    {
        foreach ($event->records as $record) {
            $this->syncUserService->syncFromPerscom($record['user_id']);
        }
    }
}

