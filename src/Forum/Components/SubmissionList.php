<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Core\Component\List\AbstractList;
use Forumify\Core\Component\List\ListResult;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\FilterObject;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('SubmissionList', '@ForumifyPerscomPlugin/frontend/components/submission_list.html.twig')]
class SubmissionList extends AbstractList
{
    #[LiveProp]
    public int $userId;

    private ?ListResult $result = null;

    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    public function getResult(): ListResult
    {
        if ($this->result !== null) {
            return $this->result;
        }

        // TODO: these params don't exist yet limit somehow.

        $submissions = $this->perscomFactory->getPerscom()
            ->submissions()
            ->search(
                filter: [new FilterObject('user_id', '=', $this->userId)],
                include: ['statuses', 'form'],
            )
            ->json();

        $this->result = new ListResult(
        // TODO: sort using API once it's a thing
            array_reverse(array_map($this->transform(...), $submissions['data'])),
            $this->page,
            $this->size,
            $submissions['meta']['total']
        );

        return $this->result;
    }

    private function transform(array $submission): array
    {
        return [
            ...$submission,
            'created_at' => new \DateTime($submission['created_at']),
        ];
    }
}
