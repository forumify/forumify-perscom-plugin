<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassInstructor;

/**
 * @extends AbstractRepository<CourseClassInstructor>
 */
class CourseClassInstructorRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return CourseClassInstructor::class;
    }
}
