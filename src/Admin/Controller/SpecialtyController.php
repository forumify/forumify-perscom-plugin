<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\SpecialtyType;
use Forumify\PerscomPlugin\Perscom\Entity\Specialty;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/specialties', 'specialty')]
#[IsGranted('perscom-io.admin.organization.specialties.view')]
class SpecialtyController extends AbstractCrudController
{
    protected string $listTemplate = '@ForumifyPerscomPlugin/admin/crud/list.html.twig';

    protected ?string $permissionView = 'perscom-io.admin.organization.specialties.view';
    protected ?string $permissionCreate = 'perscom-io.admin.organization.specialties.create';
    protected ?string $permissionEdit = 'perscom-io.admin.organization.specialties.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.organization.specialties.delete';

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Specialty::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomSpecialtyTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(SpecialtyType::class, $data);
    }
}
