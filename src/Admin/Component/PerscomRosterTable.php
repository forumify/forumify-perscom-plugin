<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Roster;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomRosterTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.organization.rosters.view')]
class PerscomRosterTable extends AbstractDoctrineTable
{
    protected ?string $permissionReorder = 'perscom-io.admin.organization.rosters.manage';

    protected function getEntityClass(): string
    {
        return Roster::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addPositionColumn()
            ->addColumn('name', [
                'field' => 'name'
            ])
            ->addColumn('actions', [
                'field' => 'id',
                'label' => '',
                'searchable' => false,
                'sortable' => false,
                'renderer' => $this->renderActions(...),
            ])
        ;
    }

    private function renderActions(int $id): string
    {
        $actions = '';
        if ($this->security->isGranted('perscom-io.admin.organization.rosters.manage')) {
            $actions .= $this->renderAction('perscom_admin_roster_edit', ['identifier' => $id], 'pencil-simple-line');
        }

        if ($this->security->isGranted('perscom-io.admin.organization.rosters.delete')) {
            $actions .= $this->renderAction('perscom_admin_roster_delete', ['identifier' => $id], 'x');
        }

        return $actions;
    }
}
