<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Scheduler;

use Forumify\PerscomPlugin\Perscom\Service\SyncService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncTaskHandler
{
    public function __construct(private readonly SyncService $syncService)
    {
    }

    public function __invoke(SyncTask $task): void
    {
        $this->syncService->sync();
    }
}
