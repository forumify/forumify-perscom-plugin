<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component\RecordTable;

use Forumify\PerscomPlugin\Perscom\Entity\Record\CombatRecord;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomAdminCombatRecordTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.records.combat_records.view')]
class CombatRecordTable extends AbstractAdminRecordTable
{
    protected function getEntityClass(): string
    {
        return CombatRecord::class;
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
        return 'combat_records';
    }
}
