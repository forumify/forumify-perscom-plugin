<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\Entity\Record\AwardRecord;
use Symfony\Component\Asset\Packages;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('AwardRecordTable', '@ForumifyPerscomPlugin/frontend/components/record_table.html.twig')]
class AwardRecordTable extends AbstractRecordTable
{
    protected array $searchFields = ['award.name'];

    public function __construct(private readonly Packages $packages)
    {
    }

    protected function getEntityClass(): string
    {
        return AwardRecord::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addDateColumn()
            ->addColumn('award', [
                'field' => 'award.name',
                'searchable' => false,
                'sortable' => false,
                'class' => 'text-small',
                'renderer' => $this->renderAward(...),
            ])
            ->addDocumentColumn(true, 'award');
    }

    private function renderAward(?string $awardName, AwardRecord $record): string
    {
        $image = $record->getAward()->getImage() ?? null;
        if ($image !== null) {
            $image = $this->packages->getUrl($image, 'perscom.asset');
        }
        $image = $image ? "<img src='$image' width='100%' height='auto' style='max-width: 24px; max-height: 24px;'>" : '';

        $awardName = $awardName ?? 'Unknown';

        return "<div class='w-100 flex items-center gap-2'>$image $awardName</div>";
    }
}
