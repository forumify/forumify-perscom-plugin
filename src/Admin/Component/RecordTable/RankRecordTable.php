<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component\RecordTable;

use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomAdminRankRecordTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.records.rank_records.view')]
class RankRecordTable extends AbstractAdminRecordTable
{
    protected function getEntityClass(): string
    {
        return RankRecord::class;
    }

    protected function addRecordColumns(): static
    {
        $this
            ->addColumn('type', [
                'field' => 'type',
                'searchable' => false,
                'sortable' => false,
            ])
            ->addColumn('rank', [
                'field' => 'rank.name',
                'searchable' => false,
                'sortable' => false,
            ])
        ;

        return $this;
    }

    protected function getRecordType(): string
    {
        return 'rank_records';
    }
}
