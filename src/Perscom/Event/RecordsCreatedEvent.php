<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event;

use Forumify\PerscomPlugin\Perscom\Entity\Record\RecordInterface;
use Symfony\Contracts\EventDispatcher\Event;

class RecordsCreatedEvent extends Event
{
    /**
     * @param array<RecordInterface> $records
     */
    public function __construct(
        public readonly array $records,
        public readonly bool $sendNotification,
    ) {
    }
}
