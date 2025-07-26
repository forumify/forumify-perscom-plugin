<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassRepository;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[AsLiveComponent('Perscom\\CourseClassList', '@ForumifyPerscomPlugin/frontend/components/course_class_list.html.twig')]
class CourseClassList extends AbstractDoctrineList
{
    #[LiveProp]
    public ?Course $course = null;

    #[LiveProp]
    public bool $signupOnly = false;

    public function __construct(
        private readonly CourseClassRepository $courseClassRepository,
    ) {
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        $qb = $this->courseClassRepository->getListQuery($this->course);
        if ($this->signupOnly) {
            $qb
                ->andWhere('cc.start > :now')
                ->andWhere(':now BETWEEN cc.signupFrom AND cc.signupUntil')
                ->setParameter('now', new DateTime());
        }

        return $qb;
    }

    protected function getCount(): int
    {
        return $this->getQueryBuilder()
            ->select('COUNT(cc)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
