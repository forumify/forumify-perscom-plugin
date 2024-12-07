<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\Operation;

/**
 * @extends AbstractRepository<Operation>
 */
class OperationRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return Operation::class;
    }

    public function createListQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('o');
        $this->addACLToQuery($qb, 'view', alias: 'o');

        $qb->orderBy('CASE
            WHEN o.start IS NULL AND o.end IS NULL THEN 0
            WHEN :now > o.start AND o.end IS NULL THEN 1
            WHEN :now BETWEEN o.start AND o.end THEN 2
            ELSE 3
            END
        ', 'ASC');
        $qb->addOrderBy('o.start', 'DESC');
        $qb->setParameter('now', (new DateTime())->format('Y-m-d'));

        return $qb;
    }
}
