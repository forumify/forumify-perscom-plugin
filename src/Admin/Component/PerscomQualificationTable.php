<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomQualificationTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.organization.qualifications.view')]
class PerscomQualificationTable extends AbstractDoctrineTable
{
    protected ?string $permissionReorder = 'perscom-io.admin.organization.qualifications.manage';

    protected function getEntityClass(): string
    {
        return Qualification::class;
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
        if ($this->security->isGranted('perscom-io.admin.organization.qualifications.manage')) {
            $actions .= $this->renderAction('perscom_admin_qualification_edit', ['identifier' => $id], 'pencil-simple-line');
        }

        if ($this->security->isGranted('perscom-io.admin.organization.qualifications.delete')) {
            $actions .= $this->renderAction('perscom_admin_qualification_delete', ['identifier' => $id], 'x');
        }

        return $actions;
    }
}
