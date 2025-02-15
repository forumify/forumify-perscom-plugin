<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Core\Component\List\AbstractList;
use Forumify\Core\Component\List\ListResult;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('Perscom\\QualificationList', '@ForumifyPerscomPlugin/frontend/components/qualification_list.html.twig')]
class QualificationList extends AbstractList
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

        $qualifications = $this->perscomFactory
            ->getPerscom()
            ->qualifications()
            ->all(['image'], $this->page, $this->size)
            ->json()
        ;

        $this->result = new ListResult(
            $qualifications['data'],
            $this->page,
            $this->size,
            $qualifications['meta']['total']
        );

        return $this->result;
    }
}
