<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Award;

class AwardType extends AbstractPerscomEntityType
{
    protected function getEntityClass(): string
    {
        return Award::class;
    }
}
