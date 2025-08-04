<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\Repository\AfterActionReportRepository;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[AsLiveComponent('Perscom\\AfterActionReportList', '@ForumifyPerscomPlugin/frontend/components/aar_list.html.twig')]
class AfterActionReportList extends AbstractDoctrineList
{
    #[LiveProp]
    public Mission $mission;

    public function __construct(
        private readonly AfterActionReportRepository $afterActionReportRepository
    ) {
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->afterActionReportRepository->createQueryBuilder('aar')
            ->where('aar.mission = :mission')
            ->join('aar.unit', 'u')
            ->orderBy('u.position')
            ->setParameter('mission', $this->mission);
    }

    protected function getCount(): int
    {
        return $this->getQueryBuilder()
            ->select('COUNT(aar)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
