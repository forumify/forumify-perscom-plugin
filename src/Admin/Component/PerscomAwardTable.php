<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Award;
use Forumify\PerscomPlugin\Perscom\Repository\AwardRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

#[AsLiveComponent('PerscomAwardTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.organization.awards.view')]
class PerscomAwardTable extends AbstractDoctrineTable
{
    public function __construct(
        private readonly Security $security,
        private readonly AwardRepository $awardRepository,
    ) {
        $this->sort = ['position' => self::SORT_ASC];
    }

    protected function getEntityClass(): string
    {
        return Award::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('position', [
                'label' => '#',
                'field' => 'id',
                'renderer' => $this->renderSortColumn(...),
                'searchable' => false,
                'class' => 'w-10'
            ])
            ->addColumn('name', [
                'field' => 'name',
                'sortable' => true
            ])
            ->addColumn('actions', [
                'label' => '',
                'field' => 'id',
                'renderer' => $this->renderActions(...),
                'sortable' => false,
                'searchable' => false,
            ]);
    }

    private function renderActions(int $id): string
    {
        if (!$this->security->isGranted('perscom-io.admin.organization.awards.manage')) {
            return '';
        }

        $actions = '';
        $actions .= $this->renderAction('perscom_admin_award_edit', ['identifier' => $id], 'pencil-simple-line');
        $actions .= $this->renderAction('perscom_admin_award_delete', ['identifier' => $id], 'x');

        return $actions;
    }

    protected function renderSortColumn(int $id): string
    {
        if (!$this->security->isGranted('perscom-io.admin.organization.awards.manage')) {
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
    #[IsGranted('perscom-io.admin.organization.awards.manage')]
    public function reorder(#[LiveArg] int $id, #[LiveArg] string $direction): void
    {
        $award = $this->awardRepository->find($id);
        if ($award === null) {
            return;
        }

        $this->awardRepository->reorder($award, $direction);
    }
}
