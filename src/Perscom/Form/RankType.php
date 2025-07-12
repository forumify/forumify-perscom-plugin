<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Rank;

class RankType extends AbstractPerscomEntityType
{
    protected function getEntityClass(): string
    {
        return Rank::class;
    }
}
