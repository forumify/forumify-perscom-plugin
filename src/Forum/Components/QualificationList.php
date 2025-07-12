<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Repository\QualificationRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('Perscom\\QualificationList', '@ForumifyPerscomPlugin/frontend/components/qualification_list.html.twig')]
class QualificationList extends AbstractDoctrineList
{
    public function __construct(private readonly QualificationRepository $qualificationRepository)
    {
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->qualificationRepository->createQueryBuilder('e');
    }

    protected function getCount(): int
    {
        return $this->qualificationRepository->count([]);
    }
}
