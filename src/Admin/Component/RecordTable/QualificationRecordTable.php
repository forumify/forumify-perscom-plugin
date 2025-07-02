<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component\RecordTable;

use Forumify\PerscomPlugin\Perscom\Entity\Record\QualificationRecord;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomAdminQualificationRecordTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.records.qualification_records.view')]
class QualificationRecordTable extends AbstractAdminRecordTable
{
    protected function getEntityClass(): string
    {
        return QualificationRecord::class;
    }

    protected function addRecordColumns(): static
    {
        $this->addColumn('qualification', [
            'field' => 'qualification.name',
            'searchable' => false,
            'sortable' => false,
        ]);

        return $this;
    }

    protected function getRecordType(): string
    {
        return 'qualification_records';
    }
}
