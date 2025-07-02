<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component\RecordTable;

use Forumify\PerscomPlugin\Perscom\Entity\Record\AwardRecord;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomAdminAwardRecordTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.records.award_records.view')]
class AwardRecordTable extends AbstractAdminRecordTable
{
    protected function getEntityClass(): string
    {
        return AwardRecord::class;
    }

    protected function addRecordColumns(): static
    {
        $this->addColumn('award', [
            'field' => 'award.name',
            'searchable' => false,
            'sortable' => false,
        ]);

        return $this;
    }

    protected function getRecordType(): string
    {
        return 'award_records';
    }
}
