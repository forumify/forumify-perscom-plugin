<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\CourseInstructor;
use Forumify\PerscomPlugin\Perscom\Repository\CourseInstructorRepository;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('Perscom\\CourseInstructorTable', '@Forumify/components/table/table.html.twig')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[IsGranted('perscom-io.admin.courses.manage')]
class CourseInstructorTable extends AbstractDoctrineTable
{
    #[LiveProp]
    public int $courseId;

    public function __construct(private readonly CourseInstructorRepository $instructorRepository)
    {
        $this->sort = ['position' => self::SORT_ASC];
    }

    protected function getEntityClass(): string
    {
        return CourseInstructor::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('position', [
                'label' => '#',
                'field' => 'id',
                'renderer' => $this->renderSortColumn(...),
                'searchable' => false,
                'class' => 'w-10',
            ])
            ->addColumn('title', [
                'field' => 'title',
            ])
            ->addColumn('actions', [
                'field' => 'id',
                'label' => '',
                'searchable' => false,
                'sortable' => false,
                'renderer' => $this->renderActions(...),
            ])
        ;
    }

    protected function getQuery(array $search): QueryBuilder
    {
        return parent::getQuery($search)
            ->andWhere('e.course = :course')
            ->setParameter('course', $this->courseId)
        ;
    }

    #[LiveAction]
    #[IsGranted('perscom-io.admin.courses.manage')]
    public function reorder(#[LiveArg] int $id, #[LiveArg] string $direction): void
    {
        $instructor = $this->instructorRepository->find($id);
        if ($instructor === null) {
            return;
        }

        $this->instructorRepository->reorder(
            $instructor,
            $direction,
            fn (QueryBuilder $qb) => $qb
                ->andWhere('e.course = :course')
                ->setParameter('course', $this->courseId),
        );
    }

    protected function renderSortColumn(int $id): string
    {
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

    private function renderActions(int $id): string
    {
        $actions = '';
        $actions .= $this->renderAction('perscom_admin_courses_edit_instructor', ['id' => $id], 'pencil-simple-line');
        $actions .= $this->renderAction('perscom_admin_courses_delete_instructor', ['id' => $id], 'x');
        return $actions;
    }
}
