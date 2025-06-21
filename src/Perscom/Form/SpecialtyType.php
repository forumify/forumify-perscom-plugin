<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Specialty;

class SpecialtyType extends AbstractPerscomEntityType
{
    protected function getEntityClass(): string
    {
        return Specialty::class;
    }
}
