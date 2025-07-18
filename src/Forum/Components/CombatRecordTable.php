<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\Entity\Record\CombatRecord;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('CombatRecordTable', '@ForumifyPerscomPlugin/frontend/components/record_table.html.twig')]
class CombatRecordTable extends AbstractRecordTable
{
    protected function getEntityClass(): string
    {
        return CombatRecord::class;
    }
}
