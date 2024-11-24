<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Scheduler;

use Forumify\PerscomPlugin\Perscom\Service\ReportInService;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('0 8 * * *', jitter: 1800)]
class ReportInTaskHandler
{
    public function __construct(private readonly ReportInService $reportInService)
    {
    }

    public function __invoke(): void
    {
        if (!$this->reportInService->isEnabled()) {
            return;
        }

        $this->reportInService->runReportInChecks();
    }
}
