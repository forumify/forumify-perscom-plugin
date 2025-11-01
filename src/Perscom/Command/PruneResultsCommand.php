<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Command;

use Forumify\Core\Command\CommandIO;
use Forumify\PerscomPlugin\Perscom\Sync\Scheduler\PruneResultsTask;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('perscom:sync:prune-results', 'Clear old sync results.')]
class PruneResultsCommand extends Command
{
    public function __construct(
        private readonly PruneResultsTask $pruneResultsTask
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new CommandIO($input, $output);
        $io->title('Clear old sync results');

        try {
            ($this->pruneResultsTask)();
        } catch (\Throwable $ex) {
            $io->error($ex->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
