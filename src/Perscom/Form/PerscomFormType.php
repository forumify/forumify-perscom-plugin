<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;

class PerscomFormType extends AbstractPerscomEntityType
{
    protected function getEntityClass(): string
    {
        return PerscomUser::class; // FIXME: obviously dont want users in here lmao
    }
}
