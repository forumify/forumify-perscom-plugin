<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Entity\AwardNominationData;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\AwardNominationRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('Perscom\\AwardNominationsList', '@ForumifyPerscomPlugin/frontend/components/award_nominations_list.html.twig')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class AwardNominationsList extends AbstractDoctrineList
{
    #[LiveProp]
    public ?Array $user;

    public function __construct(
        private readonly AwardNominationRepository $awardNominationRepository,
        private readonly PerscomUserService $userService,
        private readonly PerscomFactory $perscomFactory
    ) {
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->awardNominationRepository->getListQuery();
    }

    protected function getCount(): int
    {
        return $this->getQueryBuilder()
            ->select('COUNT(ar)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /** @return AwardNominationData[] $ */
    public function getPendingAwards(): Array
    {
        $user = $this->userService->getLoggedInPerscomUser();
        $qb = $this->getQueryBuilder()
             ->andWhere('ar.perscomUserId = :perscomUserId')
             ->orderBy('ar.createdDate', 'DESC')
             ->setParameter('perscomUserId', $user['id']);

        return $this->awardNominationRepository->getAwardNominations($qb);
    }
}
