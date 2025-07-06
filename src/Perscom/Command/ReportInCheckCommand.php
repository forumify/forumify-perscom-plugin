<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Command;

use Forumify\PerscomPlugin\Perscom\Service\ReportInService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('perscom:report-in:run-checks')]
class ReportInCheckCommand extends Command
{
    public function __construct(private readonly ReportInService $reportInService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->reportInService->isEnabled()) {
            return self::SUCCESS;
        }

        $this->reportInService->runReportInChecks();

        return self::SUCCESS;
    }
}
