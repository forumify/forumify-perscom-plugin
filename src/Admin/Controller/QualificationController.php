<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\QualificationType;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/qualifications', 'qualification')]
#[IsGranted('perscom-io.admin.organization.view')]
class QualificationController extends AbstractCrudController
{
    protected string $listTemplate = '@ForumifyPerscomPlugin/admin/crud/list.html.twig';

    protected ?string $permissionView = 'perscom-io.admin.organization.qualifications.view';
    protected ?string $permissionCreate = 'perscom-io.admin.organization.qualifications.create';
    protected ?string $permissionEdit = 'perscom-io.admin.organization.qualifications.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.organization.qualifications.delete';

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Qualification::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomQualificationTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(QualificationType::class, $data, [
            'image_required' => $data === null
        ]);
    }
}
