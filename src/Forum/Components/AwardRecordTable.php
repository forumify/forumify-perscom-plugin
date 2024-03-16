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
                'class' => 'flex items-center text-small',
                'renderer' => $this->renderAward(...),
            ])
            ->addDocumentColumn(true, 'award');
    }

    private function renderAward(string $awardName, array $record): string
    {
        $imgUrl = $record['award']['image']['image_url'] ?? null;
        $image = $imgUrl ? "<img src='$imgUrl' width='100%' height='auto'>" : '';

        return "<div class='mr-1 flex justify-center items-center' style='width: 24px; height: 24px'>$image</div>" . $awardName;
    }
}
