<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Core\Component\List\AbstractList;
use Forumify\Core\Component\List\ListResult;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('AwardList', '@ForumifyPerscomPlugin/frontend/components/award_list.html.twig')]
class AwardList extends AbstractList
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

        $awards = $this->perscomFactory
            ->getPerscom()
            ->awards()
            ->all(['image'], $this->page, $this->size)
            ->json();

        $this->result = new ListResult(
            $awards['data'],
            $this->page,
            $this->size,
            $awards['meta']['total'],
        );

        return $this->result;
    }
}
