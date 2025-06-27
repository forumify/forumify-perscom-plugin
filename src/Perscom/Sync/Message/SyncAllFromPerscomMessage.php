<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\Message;

use Forumify\Core\Messenger\AsyncMessageInterface;

class SyncAllFromPerscomMessage implements AsyncMessageInterface
{
    public function __construct(
        public readonly int $resultId,
    ) {
    }
}
