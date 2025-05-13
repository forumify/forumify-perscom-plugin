<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Perscom;
use Perscom\Contracts\Searchable;
use Perscom\Data\SortObject;

class StatusType extends AbstractPerscomEntityType
{
    protected function getResource(Perscom $perscom): Searchable
    {
        return $perscom->statuses();
    }

    protected function getSorting(): ?array
    {
        return [new SortObject('name')];
    }
}
