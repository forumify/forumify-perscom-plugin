<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('QualificationRecordTable', '@Forumify/components/table/table.html.twig')]
class QualificationRecordTable extends RecordTable
{
    protected function buildTable(): void
    {
        $this
            ->addDateColumn()
            ->addColumn('qualification', [
                'field' => '[qualification][name?]',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
            ])
            ->addDocumentColumn(true, 'qualification');
    }
}
