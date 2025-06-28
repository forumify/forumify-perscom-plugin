<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\AwardType;
use Forumify\PerscomPlugin\Perscom\Entity\Award;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\FormInterface;

#[Route('/awards', 'award')]
#[IsGranted('perscom-io.admin.organization.awards.view')]
class AwardController extends AbstractCrudController
{
    protected string $listTemplate = '@ForumifyPerscomPlugin/admin/crud/list.html.twig';

    protected ?string $permissionView = 'perscom-io.admin.organization.awards.view';
    protected ?string $permissionCreate = 'perscom-io.admin.organization.awards.create';
    protected ?string $permissionEdit = 'perscom-io.admin.organization.awards.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.organization.awards.delete';

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Award::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomAwardTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(AwardType::class, $data, [
            'image_required' => $data === null
        ]);
    }
}
