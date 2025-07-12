<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Command;

use Forumify\PerscomPlugin\Perscom\Sync\Service\SyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('perscom:sync:run', 'run PERSCOM data sync.')]
class SyncCommand extends Command
{
    public function __construct(
        private readonly SyncService $syncService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->syncService->syncAll();
        return self::SUCCESS;
    }
}
