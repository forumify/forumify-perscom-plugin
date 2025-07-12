<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Qualification;

class QualificationType extends AbstractPerscomEntityType
{
    protected function getEntityClass(): string
    {
        return Qualification::class;
    }
}
