<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

/**
 * @extends AbstractDoctrineList<Qualification>
 */
#[AsLiveComponent('Perscom\\QualificationList', '@ForumifyPerscomPlugin/frontend/components/qualification_list.html.twig')]
class QualificationList extends AbstractDoctrineList
{
    protected function getEntityClass(): string
    {
        return Qualification::class;
    }
}
