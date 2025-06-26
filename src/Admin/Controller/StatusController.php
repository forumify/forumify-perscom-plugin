<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\StatusType;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/statuses', 'status')]
#[IsGranted('perscom-io.admin.organization.view')]
class StatusController extends AbstractCrudController
{
    protected ?string $permissionView = 'perscom-io.admin.organization.statuses.view';
    protected ?string $permissionCreate = 'perscom-io.admin.organization.statuses.create';
    protected ?string $permissionEdit = 'perscom-io.admin.organization.statuses.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.organization.statuses.delete';
    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Status::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomStatusTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(StatusType::class, $data);
    }
}
