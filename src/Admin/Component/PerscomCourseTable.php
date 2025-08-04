<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[AsLiveComponent('PerscomCourseTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.courses.view')]
class PerscomCourseTable extends AbstractDoctrineTable
{
    protected ?string $permissionReorder = 'perscom-io.admin.courses.manage';

    protected function getEntityClass(): string
    {
        return Course::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addPositionColumn()
            ->addColumn('title', [
                'field' => 'title',
            ])
            ->addColumn('actions', [
                'field' => 'id',
                'label' => '',
                'renderer' => $this->renderActions(...),
                'searchable' => false,
                'sortable' => false,
            ]);
    }

    private function renderActions(int $id, Course $course): string
    {
        $actions = '';
        if ($this->security->isGranted('perscom-io.admin.courses.manage')) {
            $actions .= $this->renderAction('perscom_admin_courses_edit', ['identifier' => $id], 'pencil-simple-line');
            $actions .= $this->renderAction('forumify_admin_acl', (array)$course->getACLParameters(), 'lock-simple');
        }
        if ($this->security->isGranted('perscom-io.admin.courses.delete')) {
            $actions .= $this->renderAction('perscom_admin_courses_delete', ['identifier' => $id], 'x');
        }

        return $actions;
    }
}
