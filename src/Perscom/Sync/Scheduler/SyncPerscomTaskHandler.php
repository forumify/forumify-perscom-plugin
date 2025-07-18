<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\Scheduler;

use Forumify\Core\Notification\ContextSerializer;
use Forumify\PerscomPlugin\Perscom\Sync\Message\SyncAllFromPerscomMessage;
use Forumify\PerscomPlugin\Perscom\Sync\Message\SyncToPerscomMessage;
use Forumify\PerscomPlugin\Perscom\Sync\Service\SyncService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Scheduler\Attribute\AsCronTask;
use Throwable;

#[AsCronTask('0 5 * * *', jitter: 3600)]
#[AsMessageHandler(handles: SyncAllFromPerscomMessage::class)]
class SyncPerscomTaskHandler
{
    public function __construct(
        private readonly SyncService $syncService,
        private readonly ContextSerializer $contextSerializer,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(?SyncAllFromPerscomMessage $message = null): void
    {
        $this->syncService->syncAll($message?->resultId);
    }

    #[AsMessageHandler]
    public function syncSingleEntity(SyncToPerscomMessage $message): void
    {
        $changeSet = $this->contextSerializer->deserialize($message->changeSet);
        $changeSet['create'] = array_values(array_filter($changeSet['create']));
        $changeSet['update'] = array_values(array_filter($changeSet['update']));

        try {
            $this->syncService->syncToPerscom($changeSet);
        } catch (Throwable) {
            $this->messageBus->dispatch($message, [new DelayStamp(30000)]);
        }
    }
}
