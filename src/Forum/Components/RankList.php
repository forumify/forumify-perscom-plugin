<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

/**
 * @extends AbstractDoctrineList<Rank>
 */
#[AsLiveComponent('RankList', '@ForumifyPerscomPlugin/frontend/components/rank_list.html.twig')]
class RankList extends AbstractDoctrineList
{
    protected function getEntityClass(): string
    {
        return Rank::class;
    }
}
