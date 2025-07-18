<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event;

use Forumify\PerscomPlugin\Perscom\Entity\FormSubmission;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Symfony\Contracts\EventDispatcher\Event;

class UserEnlistedEvent extends Event
{
    public function __construct(
        public readonly PerscomUser $perscomUser,
        public readonly FormSubmission $submission,
    ) {
    }
}
