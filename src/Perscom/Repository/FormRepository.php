<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Form;

class FormRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return Form::class;
    }
}
