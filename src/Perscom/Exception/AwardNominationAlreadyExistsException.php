<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Exception;

use Exception;

class AwardNominationAlreadyExistsException extends Exception
{
    public function __construct()
    {
        parent::__construct('You have already nominated this user for this award.');
    }
}
