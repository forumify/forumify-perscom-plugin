<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component\RecordTable;

use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomAdminServiceRecordTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.records.service_records.view')]
class ServiceRecordTable extends AbstractAdminRecordTable
{
    protected function getEntityClass(): string
    {
        return ServiceRecord::class;
    }

    protected function addRecordColumns(): static
    {
        $this->addColumn('text', [
            'field' => 'text',
            'searchable' => false,
        ]);

        return $this;
    }

    protected function getRecordType(): string
    {
        return 'service_records';
    }
}
