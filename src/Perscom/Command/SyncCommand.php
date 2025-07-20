<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Command;

use Forumify\Core\Command\CommandIO;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomSyncResult;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomSyncResultRepository;
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
        private readonly PerscomSyncResultRepository $syncResultRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new CommandIO($input, $output);

        $result = new PerscomSyncResult();
        $this->syncResultRepository->save($result);

        try {
            $this->syncService->syncAll($result->getId());
        } catch (\Throwable $ex) {
            $io->error($ex->getMessage());
        }

        $io->writeln(explode('\n', $result->getErrorMessage()));

        return self::SUCCESS;
    }
}
