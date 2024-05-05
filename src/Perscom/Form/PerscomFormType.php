<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Perscom;
use Perscom\Contracts\ResourceContract;
use Perscom\Data\SortObject;

class PerscomFormType extends AbstractPerscomEntityType
{
    protected function getResource(Perscom $perscom): ResourceContract
    {
        return $perscom->forms();
    }

    protected function getSorting(): ?array
    {
        return [new SortObject('name')];
    }
}
