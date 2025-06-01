<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\AfterActionReport;
use Forumify\PerscomPlugin\Perscom\Entity\Operation;

/**
 * @extends AbstractRepository<AfterActionReport>
 */
class AfterActionReportRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return AfterActionReport::class;
    }

    /**
     * @param array<int> $unitIds
     * @param array<Operation> $operations
     * @return array<AfterActionReport>
     */
    public function findByMissionStartAndUnit(
        \DateTime $from,
        \DateTime $to,
        array $unitIds,
        array $operations,
    ): array {
        $qb = $this->createQueryBuilder('aar')
            ->join('aar.mission', 'm')
            ->join('m.operation', 'o')
            ->where('m.start BETWEEN :from AND :to')
            ->setParameters(['from' => $from, 'to' => $to])
            ->orderBy('m.start', 'ASC')
            ->addOrderBy('aar.unitPosition', 'ASC')
        ;

        if (!empty($unitIds)) {
            $qb
                ->andWhere('aar.unitId IN (:units)')
                ->setParameter('units', $unitIds)
            ;
        }

        if (!empty($operations)) {
            $qb
                ->andWhere('m.operation IN (:operations)')
                ->setParameter('operations', $operations)
            ;
        }

        return $this
            ->addACLToQuery($qb, 'view', Operation::class, 'o')
            ->getQuery()
            ->getResult();
    }
}
