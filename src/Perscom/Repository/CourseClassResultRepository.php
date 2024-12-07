<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassResult;

/**
 * @extends AbstractRepository<CourseClassResult>
 */
class CourseClassResultRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return CourseClassResult::class;
    }
}
