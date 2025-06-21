<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Position;

class PositionType extends AbstractPerscomEntityType
{
    protected function getEntityClass(): string
    {
        return Position::class;
    }
}
