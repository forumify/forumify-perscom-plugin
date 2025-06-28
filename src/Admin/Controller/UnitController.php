<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\UnitType;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/units', 'unit')]
#[IsGranted('perscom-io.admin.organization.units.view')]
class UnitController extends AbstractCrudController
{
    protected string $listTemplate = '@ForumifyPerscomPlugin/admin/crud/list.html.twig';

    protected ?string $permissionView = 'perscom-io.admin.organization.units.view';
    protected ?string $permissionCreate = 'perscom-io.admin.organization.units.create';
    protected ?string $permissionEdit = 'perscom-io.admin.organization.units.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.organization.units.delete';

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Unit::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomUnitTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(UnitType::class, $data);
    }
}
