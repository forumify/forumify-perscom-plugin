<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\Entity\Operation;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

/**
 * @extends AbstractDoctrineList<Mission>
 */
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[AsLiveComponent('Perscom\\MissionList', '@ForumifyPerscomPlugin/frontend/components/mission_list.html.twig')]
class MissionList extends AbstractDoctrineList
{
    #[LiveProp]
    public Operation $operation;

    #[LiveProp]
    public int $size = 5;

    protected function getEntityClass(): string
    {
        return Mission::class;
    }

    protected function getQuery(): QueryBuilder
    {
        return parent::getQuery()
            ->where('e.operation = :operation')
            ->orderBy('e.start', 'DESC')
            ->setParameter('operation', $this->operation);
    }
}
