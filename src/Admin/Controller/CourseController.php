<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\CourseType;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/courses', 'courses')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class CourseController extends AbstractCrudController
{
    protected ?string $permissionView = 'perscom-io.admin.courses.view';
    protected ?string $permissionCreate = 'perscom-io.admin.courses.manage';
    protected ?string $permissionEdit = 'perscom-io.admin.courses.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.courses.delete';

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Course::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomCourseTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(CourseType::class, $data);
    }
}
