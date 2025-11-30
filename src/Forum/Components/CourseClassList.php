<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

/**
 * @extends AbstractDoctrineList<CourseClass>
 */
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[AsLiveComponent('Perscom\\CourseClassList', '@ForumifyPerscomPlugin/frontend/components/course_class_list.html.twig')]
class CourseClassList extends AbstractDoctrineList
{
    #[LiveProp]
    public ?Course $course = null;

    #[LiveProp]
    public bool $signupOnly = false;

    protected string|array|null $aclPermission = [
        'permission' => 'view',
        'alias' => 'c',
        'entity' => Course::class,
    ];

    protected function getEntityClass(): string
    {
        return CourseClass::class;
    }

    protected function getQuery(): QueryBuilder
    {
        $qb = parent::getQuery()
            ->innerJoin('e.course', 'c')
            ->orderBy('e.start', 'DESC');

        if ($this->course !== null) {
            $qb->andWhere('e.course = :course')->setParameter('course', $this->course);
        }

        return $qb;
    }
}
