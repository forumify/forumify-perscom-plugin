<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('AssignmentRecordTable', '@ForumifyPerscomPlugin/frontend/components/record_table.html.twig')]
class AssignmentRecordTable extends RecordTable
{
    protected function buildTable(): void
    {
        $this
            ->addDateColumn()
            ->addColumn('unit', [
                'field' => '[unit][name]',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
            ])
            ->addColumn('position', [
                'field' => '[position][name]',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
            ]);
    }

    protected function modifyData(): void
    {
        $this->data = array_filter($this->data, static fn (array $row) => $row['position'] !== null || $row['unit'] !== null);
    }

    protected function filterData(array $fields): callable
    {
        $parentFilter = parent::filterData(['[unit][name]', '[position][name]']);
        return fn (array $record): bool => ($record['position'] ?? null) !== null
            && ($record['unit'] ?? null) !== null
            && $parentFilter($record);
    }
}
