<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\Entity\Record\QualificationRecord;
use Symfony\Component\Asset\Packages;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('QualificationRecordTable', '@ForumifyPerscomPlugin/frontend/components/record_table.html.twig')]
class QualificationRecordTable extends AbstractRecordTable
{
    protected array $searchFields = ['qualification.name'];

    public function __construct(private readonly Packages $packages)
    {
    }

    protected function getEntityClass(): string
    {
        return QualificationRecord::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addDateColumn()
            ->addColumn('qualification', [
                'field' => 'qualification.name',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-small',
                'renderer' => $this->renderQualification(...),
            ])
            ->addDocumentColumn(true, 'qualification');
    }

    private function renderQualification(string $qualificationName, QualificationRecord $record): string
    {
        $image = $record->getQualification()->getImage() ?? null;
        if ($image !== null) {
            $image = $this->packages->getUrl($image, 'perscom.asset');
        }
        $image = $image ? "<img src='$image' width='100%' height='auto' style='max-width: 24px; max-height: 24px;'>" : '';

        $qualificationName = $qualificationName ?? 'Unknown';

        return "<div class='w-100 flex items-center gap-2'>$image $qualificationName</div>";
    }
}
