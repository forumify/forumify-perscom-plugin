<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Entity\Operation;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRepository;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('Perscom\\UpcomingMissionList', '@ForumifyPerscomPlugin/frontend/components/upcoming_mission_list.html.twig')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class UpcomingMissionList extends AbstractDoctrineList
{
    #[LiveProp]
    public int $size = 5;

    public function __construct(private readonly MissionRepository $missionRepository)
    {
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->missionRepository->createQueryBuilder('m')
            ->where('m.start > CURRENT_TIMESTAMP()')
            ->orderBy('m.start', 'ASC');
    }

    protected function getCount(): int
    {
        return $this->getQueryBuilder()
            ->select('COUNT(m)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
