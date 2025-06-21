<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Unit;

class UnitType extends AbstractPerscomEntityType
{
    protected function getEntityClass(): string
    {
        return Unit::class;
    }
}
