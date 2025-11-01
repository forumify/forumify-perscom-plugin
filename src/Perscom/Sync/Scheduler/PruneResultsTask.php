<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\Scheduler;

use Forumify\PerscomPlugin\Perscom\Repository\PerscomSyncResultRepository;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('0 10 * * *', jitter: 300)]
class PruneResultsTask
{
    public function __construct(
        private readonly PerscomSyncResultRepository $resultRepository
    ) {
    }

    public function __invoke(): void
    {
        $this->resultRepository->deleteRunningResults();
        $this->resultRepository->deleteOldResults();
    }
}
