<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Core\Component\List\AbstractList;
use Forumify\Core\Component\List\ListResult;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('RankList', '@ForumifyPerscomPlugin/frontend/components/rank_list.html.twig')]
class RankList extends AbstractList
{
    private ?ListResult $result = null;

    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    public function getResult(): ListResult
    {
        if ($this->result !== null) {
            return $this->result;
        }

        $ranks = $this->perscomFactory
            ->getPerscom()
            ->ranks()
            ->all(['image'], $this->page, $this->size)
            ->json();

        $this->result = new ListResult(
            $ranks['data'],
            $this->page,
            $this->size,
            $ranks['meta']['total'],
        );

        return $this->result;
    }
}
