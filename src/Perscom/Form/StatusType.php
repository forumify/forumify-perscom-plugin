<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Status;

class StatusType extends AbstractPerscomEntityType
{
    protected function getEntityClass(): string
    {
        return Status::class;
    }
}
