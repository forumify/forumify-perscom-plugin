<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\PositionType;
use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/positions', 'position')]
#[IsGranted('perscom-io.admin.organization.positions.view')]
class PositionController extends AbstractCrudController
{
    protected ?string $permissionView = 'perscom-io.admin.organization.positions.view';
    protected ?string $permissionCreate = 'perscom-io.admin.organization.positions.create';
    protected ?string $permissionEdit = 'perscom-io.admin.organization.positions.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.organization.positions.delete';

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Position::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomPositionTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(PositionType::class, $data);
    }
}
