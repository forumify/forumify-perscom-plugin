<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UserEnlistedEvent extends Event
{
    public function __construct(
        public readonly array $perscomUser,
        public readonly array $submission,
    ) {
    }
}
