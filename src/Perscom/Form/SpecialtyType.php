<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Perscom;
use Perscom\Contracts\ResourceContract;

class SpecialtyType extends AbstractPerscomEntityType
{
    protected function getResource(Perscom $perscom): ResourceContract
    {
        return $perscom->specialties();
    }
}
