<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\DocumentType;
use Forumify\PerscomPlugin\Perscom\Entity\Document;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/documents', 'document')]
class DocumentController extends AbstractCrudController
{
    protected string $listTemplate = '@ForumifyPerscomPlugin/admin/crud/list.html.twig';

    protected ?string $permissionView = 'perscom-io.admin.organization.documents.view';
    protected ?string $permissionCreate = 'perscom-io.admin.organization.documents.create';
    protected ?string $permissionEdit = 'perscom-io.admin.organization.documents.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.organization.documents.delete';

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Document::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomDocumentTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(DocumentType::class, $data);
    }
}
