<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('AwardRecordTable', '@Forumify/components/table/table.html.twig')]
class AwardRecordTable extends RecordTable
{
    protected function buildTable(): void
    {
        $this
            ->addDateColumn()
            ->addColumn('award', [
                'field' => '[award?][name]',
                'searchable' => false,
                'sortable' => false,
                'class' => 'flex items-center text-small'
            ]);
    }
}
