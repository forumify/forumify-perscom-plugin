<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Form;

class PerscomFormType extends AbstractPerscomEntityType
{
    protected function getEntityClass(): string
    {
        return Form::class;
    }
}
