<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Document;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

use function Symfony\Component\String\u;

#[IsGranted('perscom-io.admin.organization.documents.view')]
#[AsLiveComponent('PerscomDocumentTable', '@Forumify/components/table/table.html.twig')]
class PerscomDocumentTable extends AbstractDoctrineTable
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    protected function getEntityClass(): string
    {
        return Document::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('name', [
                'field' => 'name',
                'renderer' => $this->renderName(...),
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

    private function renderName(string $name, Document $document): string
    {
        $description = u(strip_tags($document->getDescription()))->truncate(1000, '...', true);
        return "<p>$name</p><p class='text-small'>{$description}</p>";
    }

    private function renderActions(int $id): string
    {
        $actions = '';

        if ($this->security->isGranted('perscom-io.admin.organization.documents.manage')) {
            $actions .= $this->renderAction('perscom_admin_document_edit', ['identifier' => $id], 'pencil-simple-line');
        }
        if ($this->security->isGranted('perscom-io.admin.organization.documents.delete')) {
            $actions .= $this->renderAction('perscom_admin_document_delete', ['identifier' => $id], 'x');
        }

        return $actions;
    }
}
