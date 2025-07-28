<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Repository\RankRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('RankList', '@ForumifyPerscomPlugin/frontend/components/rank_list.html.twig')]
class RankList extends AbstractDoctrineList
{
    public function __construct(private readonly RankRepository $rankRepository)
    {
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->rankRepository
            ->createQueryBuilder('e')
            ->addOrderBy('e.position', 'ASC')
        ;
    }

    protected function getCount(): int
    {
        return $this->rankRepository->count([]);
    }
}
