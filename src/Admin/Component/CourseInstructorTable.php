<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\CourseInstructor;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[AsLiveComponent('Perscom\\CourseInstructorTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.courses.manage')]
class CourseInstructorTable extends AbstractDoctrineTable
{
    #[LiveProp]
    public int $courseId;

    protected ?string $permissionReorder = 'perscom-io.admin.courses.manage';

    protected function getEntityClass(): string
    {
        return CourseInstructor::class;
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

    protected function reorderItem(object $entity, string $direction): void
    {
        $this->repository->reorder(
            $entity,
            $direction,
            fn (QueryBuilder $qb) => $qb
                ->andWhere('e.course = :course')
                ->setParameter('course', $this->courseId),
        );
    }

    private function renderActions(int $id): string
    {
        $actions = '';
        $actions .= $this->renderAction('perscom_admin_courses_edit_instructor', ['id' => $id], 'pencil-simple-line');
        $actions .= $this->renderAction('perscom_admin_courses_delete_instructor', ['id' => $id], 'x');
        return $actions;
    }
}
