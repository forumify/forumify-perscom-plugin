<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RecordsCreatedEvent extends Event
{
    public function __construct(
        public readonly string $type,
        public readonly array $records,
        public readonly bool $sendNotification,
    ) {
    }
}
