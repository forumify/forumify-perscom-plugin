<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\PerscomPlugin\Perscom\Repository\CourseRepository;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

#[AsLiveComponent('PerscomCourseTable', '@Forumify/components/table/table.html.twig')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[IsGranted('perscom-io.admin.courses.view')]
class PerscomCourseTable extends AbstractDoctrineTable
{
    public function __construct(
        private readonly Security $security,
        private readonly CourseRepository $courseRepository,
    ) {
    }

    protected function getEntityClass(): string
    {
        return Course::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('position', [
                'class' => 'w-10',
                'field' => 'id',
                'label' => '#',
                'renderer' => $this->renderSortColumn(...),
                'searchable' => false,
            ])
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

    #[LiveAction]
    #[IsGranted('perscom-io.admin.courses.manage')]
    public function reorder(#[LiveArg]
    int $id, #[LiveArg]
    string $direction): void
    {
        $course = $this->courseRepository->find($id);
        if ($course === null) {
            return;
        }

        $this->courseRepository->reorder($course, $direction);
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

    private function renderSortColumn(int $id): string
    {
        if (!$this->security->isGranted('perscom-io.admin.courses.manage')) {
            return '';
        }

        return '
            <button
                class="btn-link btn-small btn-icon p-1"
                data-action="live#action"
                data-live-action-param="reorder"
                data-live-id-param="' . $id . '"
                data-live-direction-param="down"
            >
                <i class="ph ph-arrow-down"></i>
            </button>
            <button
                class="btn-link btn-small btn-icon p-1"
                data-action="live#action"
                data-live-action-param="reorder"
                data-live-id-param="' . $id . '"
                data-live-direction-param="up"
            >
                <i class="ph ph-arrow-up"></i>
            </button>';
    }
}
