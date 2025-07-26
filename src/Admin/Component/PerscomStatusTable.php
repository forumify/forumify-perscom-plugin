<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Forumify\PerscomPlugin\Perscom\Repository\StatusRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Twig\Environment;

#[AsLiveComponent('PerscomStatusTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.organization.statuses.view')]
class PerscomStatusTable extends AbstractDoctrineTable
{
    public function __construct(
        private readonly Environment $twig,
        private readonly Security $security,
        private readonly StatusRepository $statusRepository
    ) {
        $this->sort = ['position' => self::SORT_ASC];
    }

    protected function getEntityClass(): string
    {
        return Status::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('position', [
                'class' => 'w-10',
                'field' => 'id',
                'label' => '#',
                'renderer' => $this->renderSortColumn(...),
                'searchable' => false,
            ])
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
        if (!$this->security->isGranted('perscom-io.admin.organization.statuses.manage')) {
            return '';
        }

        $actions = '';
        $actions .= $this->renderAction('perscom_admin_status_edit', ['identifier' => $id], 'pencil-simple-line');
        $actions .= $this->renderAction('perscom_admin_status_delete', ['identifier' => $id], 'x');

        return $actions;
    }

    protected function renderSortColumn(int $id): string
    {
        if (!$this->security->isGranted('perscom-io.admin.organization.statuses.manage')) {
            return '';
        }

        return '
            <button
                class="btn-link btn-small btn-icon p-1"
                data-action="live#action"
                data-live-action-param="reorder"
                data-live-id-param="' . $id . '"
                data-live-direction-param="down"
            >
                <i class="ph ph-arrow-down"></i>
            </button>
            <button
                class="btn-link btn-small btn-icon p-1"
                data-action="live#action"
                data-live-action-param="reorder"
                data-live-id-param="' . $id . '"
                data-live-direction-param="up"
            >
                <i class="ph ph-arrow-up"></i>
            </button>';
    }

    #[LiveAction]
    #[IsGranted('perscom-io.admin.organization.statuses.manage')]
    public function reorder(#[LiveArg] int $id, #[LiveArg] string $direction): void
    {
        $status = $this->statusRepository->find($id);
        if ($status === null) {
            return;
        }

        $this->statusRepository->reorder($status, $direction);
    }
}
