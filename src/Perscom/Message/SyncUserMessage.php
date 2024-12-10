<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Message;

use Forumify\Core\Messenger\AsyncMessageInterface;

class SyncUserMessage implements AsyncMessageInterface
{
    public function __construct(
        public ?int $perscomUserId = null,
        public ?int $forumifyUserId = null,
    ) {
    }
}
