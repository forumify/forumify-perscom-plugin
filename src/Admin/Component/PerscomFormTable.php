<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomFormTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.organization.forms.view')]
class PerscomFormTable extends AbstractDoctrineTable
{
    protected function getEntityClass(): string
    {
        return Form::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('name', [
                'field' => 'name',
            ])
            ->addColumn('submissions', [
                'field' => 'id',
                'renderer' => fn($_, Form $form) => $form->getSubmissions()->count(),
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

    private function renderActions(int $id): string
    {
        $actions = '';

        if ($this->security->isGranted('perscom-io.admin.organization.forms.manage')) {
            $actions .= $this->renderAction('perscom_admin_form_edit', ['identifier' => $id], 'pencil-simple-line');
            $actions .= $this->renderAction('perscom_admin_form_field_list', ['formId' => $id], 'textbox');
        }

        if ($this->security->isGranted('perscom-io.admin.organization.forms.delete')) {
            $actions .= $this->renderAction('perscom_admin_form_delete', ['identifier' => $id], 'x');
        }

        return $actions;
    }
}
