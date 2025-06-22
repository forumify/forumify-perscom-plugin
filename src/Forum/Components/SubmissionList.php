<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Repository\FormSubmissionRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('SubmissionList', '@ForumifyPerscomPlugin/frontend/components/submission_list.html.twig')]
class SubmissionList extends AbstractDoctrineList
{
    #[LiveProp]
    public int $userId;

    public function __construct(
        private readonly FormSubmissionRepository $formSubmissionRepository,
    ) {
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this
            ->formSubmissionRepository
            ->createQueryBuilder('e')
            ->where('e.user = :user')
            ->setParameter('user', $this->userId)
            ->orderBy('e.createdAt', 'DESC')
        ;
    }

    protected function getCount(): int
    {
        return $this
            ->getQueryBuilder()
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0
        ;
    }
}
