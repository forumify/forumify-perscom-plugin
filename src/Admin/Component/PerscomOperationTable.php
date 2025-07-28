<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use DateTime;
use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Operation;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomOperationTable', '@Forumify/components/table/table.html.twig')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[IsGranted('perscom-io.admin.operations.view')]
class PerscomOperationTable extends AbstractDoctrineTable
{
    public function __construct()
    {
        $this->sort = ['start' => 'DESC'];
    }

    protected function getEntityClass(): string
    {
        return Operation::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('title', [
                'field' => 'title',
            ])
            ->addColumn('start', [
                'field' => 'start',
                'renderer' => fn (?DateTime $start) => $start?->format('Y-m-d'),
            ])
            ->addColumn('end', [
                'field' => 'end',
                'renderer' => fn (?DateTime $start) => $start?->format('Y-m-d'),
            ])
            ->addColumn('actions', [
                'label' => '',
                'field' => 'id',
                'searchable' => false,
                'sortable' => false,
                'renderer' => $this->renderActions(...),
            ]);
    }

    private function renderActions(int $id, Operation $operation): string
    {
        $actions = '';
        if ($this->security->isGranted('perscom-io.admin.operations.manage')) {
            $actions .= $this->renderAction('perscom_admin_operations_edit', ['identifier' => $id], 'pencil-simple-line');
            $actions .= $this->renderAction('forumify_admin_acl', (array)$operation->getACLParameters(), 'lock-simple');
        }

        if ($this->security->isGranted('perscom-io.admin.operations.delete')) {
            $actions .= $this->renderAction('perscom_admin_operations_delete', ['identifier' => $id], 'x');
        }

        return $actions;
    }
}
