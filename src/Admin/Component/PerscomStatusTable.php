<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Twig\Environment;

#[AsLiveComponent('PerscomStatusTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.organization.statuses.view')]
class PerscomStatusTable extends AbstractDoctrineTable
{
    protected ?string $permissionReorder = 'perscom-io.admin.organization.statuses.manage';

    public function __construct(private readonly Environment $twig)
    {
    }

    protected function getEntityClass(): string
    {
        return Status::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addPositionColumn()
            ->addColumn('name', [
                'field' => 'name',
                'sortable' => true,
            ])
            ->addColumn('appearance', [
                'renderer' => fn ($_, $status) => $this->twig->render('@ForumifyPerscomPlugin/frontend/roster/components/status.html.twig', ['status' => $status]),
                'searchable' => false,
                'sortable' => false,
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
        if ($this->security->isGranted('perscom-io.admin.organization.statuses.manage')) {
            $actions .= $this->renderAction('perscom_admin_status_edit', ['identifier' => $id], 'pencil-simple-line');
        }

        if ($this->security->isGranted('perscom-io.admin.organization.statuses.delete')) {
            $actions .= $this->renderAction('perscom_admin_status_delete', ['identifier' => $id], 'x');
        }

        return $actions;
    }
}
