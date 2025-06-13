<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\Scheduler;

use Forumify\PerscomPlugin\Perscom\Sync\Message\SyncPerscomDataMessage;
use Forumify\PerscomPlugin\Perscom\Sync\Service\SyncService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('0 5 * * *', jitter: 3600)]
#[AsMessageHandler(handles: SyncPerscomDataMessage::class)]
class SyncPerscomTaskHandler
{
    public function __construct(private readonly SyncService $syncService)
    {
    }

    public function __invoke(): void
    {
        $this->syncService->sync();
    }
}
