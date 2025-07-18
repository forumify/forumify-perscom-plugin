<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;

class UserType extends AbstractPerscomEntityType
{
    protected function getEntityClass(): string
    {
        return PerscomUser::class;
    }
}
