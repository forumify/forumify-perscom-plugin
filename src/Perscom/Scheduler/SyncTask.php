<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Scheduler;

use DateInterval;
use Forumify\Core\Scheduler\TaskInterface;

class SyncTask implements TaskInterface
{
    public function getFrequency(): string|int|DateInterval
    {
        return '12 hours';
    }
}
