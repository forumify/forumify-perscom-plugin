<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomPositionTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.organization.positions.view')]
class PerscomPositionTable extends AbstractDoctrineTable
{
    protected ?string $permissionReorder = 'perscom-io.admin.organization.positions.manage';

    protected function getEntityClass(): string
    {
        return Position::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addPositionColumn()
            ->addColumn('name', [
                'field' => 'name',
                'sortable' => true,
            ])
            ->addColumn('actions', [
                'field' => 'id',
                'label' => '',
                'renderer' => $this->renderActions(...),
                'searchable' => false,
                'sortable' => false,
            ]);
    }

    private function renderActions(int $id): string
    {
        $actions = '';
        if ($this->security->isGranted('perscom-io.admin.organization.positions.manage')) {
            $actions .= $this->renderAction('perscom_admin_position_edit', ['identifier' => $id], 'pencil-simple-line');
        }

        if ($this->security->isGranted('perscom-io.admin.organization.positions.delete')) {
            $actions .= $this->renderAction('perscom_admin_position_delete', ['identifier' => $id], 'x');
        }

        return $actions;
    }
}
