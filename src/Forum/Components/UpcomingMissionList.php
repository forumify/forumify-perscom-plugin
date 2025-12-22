<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

/**
 * @extends AbstractDoctrineList<Mission>
 */
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[AsLiveComponent('Perscom\\UpcomingMissionList', '@ForumifyPerscomPlugin/frontend/components/upcoming_mission_list.html.twig')]
class UpcomingMissionList extends AbstractDoctrineList
{
    #[LiveProp]
    public int $size = 5;

    protected function getEntityClass(): string
    {
        return Mission::class;
    }

    protected function getQuery(): QueryBuilder
    {
        return parent::getQuery()
            ->where('e.start > :start')
            ->setParameter('start', new DateTime())
            ->orderBy('e.start', 'ASC');
    }
}
