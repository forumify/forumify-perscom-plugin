<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\AwardNomination;
use Forumify\PerscomPlugin\Perscom\Entity\AwardNominationData;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;

class AwardNominationRepository extends AbstractRepository
{
    private Array $usersCache;
    private Array $statusesCache;
    private Array $awardsCache;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly PerscomFactory $perscomFactory) {
            parent::__construct($managerRegistry);
            $this->usersCache = [];
    }

    public static function getEntityClass(): string
    {
        return AwardNomination::class;
    }

    public function getListQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('ar');
    }

    /** @return AwardNominationData[] $ */
    public function getAwardNominations(QueryBuilder $qb): Array {
        $dbResult = $qb->getQuery()->getArrayResult();
        
        $perscom = $this->perscomFactory->getPerscom();
        $this->statusesCache = $perscom->statuses()->all()->json('data');
        $this->awardsCache = $perscom->awards()->all(include: ['image'])->json('data');

        $rows = [];
        foreach ($dbResult as $row) {
            $nomination = AwardNomination::fromArray($row);
            $data = $this->resolveData($nomination);
            array_push($rows, $data);
        }

        return $rows;
    }

    private function resolveData(AwardNomination $nomination): AwardNominationData
    {
        $data = new AwardNominationData();
        $data->nomination = $nomination;
        $data->requesterItem = $this->getPerscomUser($nomination->getPerscomUserId());
        $data->receiverItem = $this->getPerscomUser($nomination->getReceiverUserId());

        if ($nomination->getUpdatedByUserId()) {
            $data->updatedByItem = $this->getPerscomUser($nomination->getUpdatedByUserId());
        }

        $data->awardItem = array_find($this->awardsCache, function($x) use ($nomination) { return $x['id'] == $nomination->getAwardId(); });
        
        $status = array_find($this->statusesCache, function ($x) use ($nomination) {
            return $x['id'] == $nomination->getStatus();
        });

        $data->statusItem = $status;

        return $data;
    }

    public function getAwardNomination(int $id): ?AwardNominationData
    {
        $qb = $this->getListQuery()
        ->andWhere('ar.id = :id')
        ->setParameter('id', $id);

        $nominations = $this->getAwardNominations($qb);
        if (count($nominations) > 0) {
            return $nominations[0];
        }

        return null;
    }

    // Because of limit in perscom api, we need to be able to navigate all the pages until there are no more
    private function getPerscomUser(int $id, ?int $lastPage = null): ?Array {
        // page in perscom api is 1-based
        $lastLocalPage = count($this->usersCache) + 1;
        foreach ($this->usersCache as $pageData) {
            $user = array_find($pageData, function($x) use ($id) { return $x['id'] == $id; });
            if ($user != null)
                return $user;
        }

        // If we are at last page we don't wanna query another time
        if ($lastLocalPage == $lastPage) {
            return null;
        }

        $perscom = $this->perscomFactory->getPerscom();
        $result = $perscom->users()->all(page: $lastLocalPage, limit: 100)->json();
        $meta = $result['meta'];
        $lastPage = $meta['last_page'];
        $data = $result['data'];
        $this->usersCache[$lastLocalPage] = $data;

        return $this->getPerscomUser($id, $lastPage);
    }
}
