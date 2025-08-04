<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\FormType;
use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/forms', 'form')]
class FormController extends AbstractCrudController
{
    protected string $listTemplate = '@ForumifyPerscomPlugin/admin/crud/list.html.twig';

    protected ?string $permissionView = 'perscom-io.admin.organization.forms.view';
    protected ?string $permissionCreate = 'perscom-io.admin.organization.forms.create';
    protected ?string $permissionEdit = 'perscom-io.admin.organization.forms.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.organization.forms.delete';

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Form::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomFormTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(FormType::class, $data);
    }
}
