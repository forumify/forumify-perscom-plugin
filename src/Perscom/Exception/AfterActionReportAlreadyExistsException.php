<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Exception;

use Exception;

class AfterActionReportAlreadyExistsException extends Exception
{
    public function __construct()
    {
        parent::__construct('An AAR for this combat unit already exists on this mission.');
    }
}
