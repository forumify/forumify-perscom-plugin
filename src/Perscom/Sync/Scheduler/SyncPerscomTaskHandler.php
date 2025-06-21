<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\Scheduler;

use Doctrine\ORM\EntityManagerInterface;
use Forumify\Core\Notification\ContextSerializer;
use Forumify\PerscomPlugin\Perscom\Sync\Exception\SyncLockedException;
use Forumify\PerscomPlugin\Perscom\Sync\Message\SyncToPerscomMessage;
use Forumify\PerscomPlugin\Perscom\Sync\Service\SyncService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('0 5 * * *', jitter: 3600)]
class SyncPerscomTaskHandler
{
    public function __construct(
        private readonly SyncService $syncService,
        private readonly EntityManagerInterface $entityManager,
        private readonly ContextSerializer $contextSerializer,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(): void
    {
        $this->syncService->syncAll();
    }

    #[AsMessageHandler(handles: SyncToPerscomMessage::class)]
    public function syncSingleEntity(SyncToPerscomMessage $message): void
    {
        $changeSet = $this->contextSerializer->deserialize($message->changeSet);

        try {
            $this->syncService->syncToPerscom($changeSet);
        } catch (SyncLockedException) {
            $this->messageBus->dispatch($message, [new DelayStamp(10000)]);
        }
    }
}
