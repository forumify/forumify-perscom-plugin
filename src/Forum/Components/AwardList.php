<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Entity\Award;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

/**
 * @extends AbstractDoctrineList<Award>
 */
#[AsLiveComponent('AwardList', '@ForumifyPerscomPlugin/frontend/components/award_list.html.twig')]
class AwardList extends AbstractDoctrineList
{
    protected function getEntityClass(): string
    {
        return Award::class;
    }
}
