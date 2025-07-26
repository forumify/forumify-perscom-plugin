<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Forumify\PerscomPlugin\Perscom\Entity\FormField;
use Forumify\PerscomPlugin\Perscom\Repository\FormFieldRepository;
use Forumify\PerscomPlugin\Perscom\Sync\Service\SyncService;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('PerscomFormFieldTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.organization.forms.manage')]
class PerscomFormFieldTable extends AbstractDoctrineTable
{
    #[LiveProp]
    public Form $form;

    public function __construct(
        private readonly FormFieldRepository $formFieldRepository,
        private readonly SyncService $syncService,
    ) {
    }

    protected function getEntityClass(): string
    {
        return FormField::class;
    }

    protected function buildTable(): void
    {
        if (!$this->syncService->isSyncEnabled()) {
            $this->addColumn('position', [
                'class' => 'w-10',
                'field' => 'id',
                'label' => '#',
                'renderer' => $this->renderSortColumn(...),
                'searchable' => false,
            ]);
        }

        $this
            ->addColumn('label', [
                'field' => 'label',
            ])
            ->addColumn('type', [
                'field' => 'type',
            ])
            ->addColumn('actions', [
                'field' => 'id',
                'label' => '',
                'renderer' => $this->renderActions(...),
                'searchable' => false,
                'sortable' => false,
            ])
        ;
    }

    protected function getQuery(array $search): QueryBuilder
    {
        return parent::getQuery($search)
            ->andWhere('e.form = :form')
            ->setParameter('form', $this->form)
        ;
    }

    private function renderSortColumn(int $id): string
    {
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
    public function reorder(#[LiveArg] int $id, #[LiveArg] string $direction): void
    {
        $field = $this->formFieldRepository->find($id);
        if ($field === null) {
            return;
        }

        $this->formFieldRepository->reorder($field, $direction);
    }

    private function renderActions(int $id): string
    {
        if ($this->syncService->isSyncEnabled()) {
            return '';
        }

        $actions = '';
        $actions .= $this->renderAction('perscom_admin_form_field_edit', ['formId' => $this->form->getId(), 'identifier' => $id], 'pencil-simple-line');
        $actions .= $this->renderAction('perscom_admin_form_field_delete', ['formId' => $this->form->getId(), 'identifier' => $id], 'x');
        return $actions;
    }
}
