<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component\RecordTable;

use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomAdminAssignmentRecordTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.records.assignment_records.view')]
class AssignmentRecordTable extends AbstractAdminRecordTable
{
    protected function getEntityClass(): string
    {
        return AssignmentRecord::class;
    }

    protected function addRecordColumns(): static
    {
        $this
            ->addColumn('type', [
                'field' => 'type',
            ])
            ->addColumn('status', [
                'field' => 'status?.name',
            ])
            ->addColumn('unit', [
                'field' => 'unit?.name',
            ])
            ->addColumn('position', [
                'field' => 'position?.name',
            ])
            ->addColumn('specialty', [
                'field' => 'specialty?.name',
            ])
        ;

        return $this;
    }

    protected function getRecordType(): string
    {
        return 'assignment_records';
    }
}
