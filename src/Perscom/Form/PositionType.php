<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Perscom;
use Perscom\Contracts\Searchable;

class PositionType extends AbstractPerscomEntityType
{
    protected function getResource(Perscom $perscom): Searchable
    {
        return $perscom->positions();
    }
}
