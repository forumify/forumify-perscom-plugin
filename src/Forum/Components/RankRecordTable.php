<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('RankRecordTable', '@Forumify/components/table/table.html.twig')]
class RankRecordTable extends RecordTable
{
    protected function buildTable(): void
    {
        $this
            ->addDateColumn()
            ->addColumn('rank', [
                'field' => '[rank][name]',
                'searchable' => false,
                'sortable' => false,
                'renderer' => $this->renderRank(...),
                'class' => 'flex items-center text-small'
            ])
            ->addColumn('type', [
                'field' => '[type]',
                'searchable' => false,
                'sortable' => false,
                'renderer' => fn (int $type) => $this->translator->trans('perscom.rank.type.' . ($type === 0 ? 'promotion' : 'demotion')),
                'class' => 'text-left text-small'
            ])
            ->addDocumentColumn(true, 'rank');
    }

    private function renderRank(string $rankName, array $record): string
    {
        $imgUrl = $record['rank']['image']['image_url'] ?? null;
        $image = $imgUrl ? "<img src='$imgUrl' width='100%' height='auto'>" : '';

        return "<div class='mr-1 flex justify-center items-center' style='width: 24px; height: 24px'>$image</div>" . $rankName;
    }
}
