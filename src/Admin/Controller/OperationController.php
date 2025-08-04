<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\OperationType;
use Forumify\PerscomPlugin\Perscom\Entity\Operation;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;

#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[Route('/operations', 'operations')]
class OperationController extends AbstractCrudController
{
    protected ?string $permissionView = 'perscom-io.admin.operations.view';
    protected ?string $permissionCreate = 'perscom-io.admin.operations.manage';
    protected ?string $permissionEdit = 'perscom-io.admin.operations.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.operations.delete';

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Operation::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomOperationTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(OperationType::class, $data);
    }
}
