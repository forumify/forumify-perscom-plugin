<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Repository\AwardRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('AwardList', '@ForumifyPerscomPlugin/frontend/components/award_list.html.twig')]
class AwardList extends AbstractDoctrineList
{
    public function __construct(private readonly AwardRepository $awardRepository)
    {
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->awardRepository
            ->createQueryBuilder('e')
            ->addOrderBy('e.position', 'ASC')
        ;
    }

    protected function getCount(): int
    {
        return $this->awardRepository->count([]);
    }
}
