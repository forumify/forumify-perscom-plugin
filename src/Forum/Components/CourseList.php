<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassRepository;
use Forumify\PerscomPlugin\Perscom\Repository\CourseRepository;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[AsLiveComponent('Perscom\\CourseList', '@ForumifyPerscomPlugin/frontend/components/course_list.html.twig')]
class CourseList extends AbstractDoctrineList
{
    #[LiveProp]
    public bool $expanded = true;

    public function __construct(
        private readonly CourseRepository $courseRepository,
        private readonly CourseClassRepository $courseClassRepository,
    ) {
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->courseRepository->getListQueryBuilder();
    }

    protected function getCount(): int
    {
        return $this->getQueryBuilder()
            ->select('COUNT(c)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function lastClass(Course $course): ?DateTime
    {
        return $this->courseClassRepository
            ->findLastClassByCourse($course)
            ?->getStart();
    }
}
