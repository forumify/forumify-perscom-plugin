<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Forumify\PerscomPlugin\Perscom\Entity\FormField;
use Forumify\PerscomPlugin\Perscom\Sync\Service\SyncService;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('PerscomFormFieldTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.organization.forms.manage')]
class PerscomFormFieldTable extends AbstractDoctrineTable
{
    #[LiveProp]
    public Form $form;

    protected ?string $permissionReorder = 'perscom-io.admin.organization.forms.manage';

    public function __construct(private readonly SyncService $syncService)
    {
        $this->sort = ['position' => self::SORT_ASC];
    }

    protected function getEntityClass(): string
    {
        return FormField::class;
    }

    protected function buildTable(): void
    {
        if (!$this->syncService->isSyncEnabled()) {
            $this->addPositionColumn();
        } else {
            $this->addColumn('position', [
                'class' => 'w-5',
                'field' => 'position',
                'label' => '#',
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

    protected function reorderItem(object $entity, string $direction): void
    {
        $this->repository->reorder(
            $entity,
            $direction,
            fn(QueryBuilder $qb) => $qb
                ->andWhere('e.form = :form')
                ->setParameter('form', $this->form),
        );
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
