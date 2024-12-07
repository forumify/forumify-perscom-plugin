<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\AfterActionReport;

/**
 * @extends AbstractRepository<AfterActionReport>
 */
class AfterActionReportRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return AfterActionReport::class;
    }
}
