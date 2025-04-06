<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('QualificationRecordTable', '@ForumifyPerscomPlugin/frontend/components/record_table.html.twig')]
class QualificationRecordTable extends RecordTable
{
    protected function buildTable(): void
    {
        $this
            ->addDateColumn()
            ->addColumn('qualification', [
                'field' => '[qualification][name]',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
            ])
            ->addDocumentColumn(true, 'qualification');
    }

    protected function filterData(array $fields): callable
    {
        $parentFilter = parent::filterData(['[qualification][name]']);
        return fn (array $record): bool => ($record['qualification'] ?? null) !== null && $parentFilter($record);
    }
}
