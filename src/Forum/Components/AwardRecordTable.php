<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('AwardRecordTable', '@ForumifyPerscomPlugin/frontend/components/record_table.html.twig')]
class AwardRecordTable extends RecordTable
{
    protected function buildTable(): void
    {
        $this
            ->addDateColumn()
            ->addColumn('award', [
                'field' => '[award][name]',
                'searchable' => false,
                'sortable' => false,
                'class' => 'text-small',
                'renderer' => $this->renderAward(...),
            ])
            ->addDocumentColumn(true, 'award');
    }

    private function renderAward(?string $awardName, array $record): string
    {
        $imgUrl = $record['award']['image']['image_url'] ?? null;
        $image = $imgUrl ? "<img src='$imgUrl' width='100%' height='auto' style='max-width: 24px; max-height: 24px;'>" : '';

        $awardName = $awardName ?? 'Unknown';

        return "<div class='w-100 flex items-center gap-2'>$image $awardName</div>";
    }

    protected function filterData(array $fields): callable
    {
        $parentFilter = parent::filterData(['[award][name]']);
        return fn (array $record): bool => ($record['award'] ?? null) !== null && $parentFilter($record);
    }
}
