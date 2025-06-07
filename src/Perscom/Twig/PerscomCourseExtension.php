<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PerscomCourseExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('perscom_course_prerequisites', [PerscomCourseExtensionRuntime::class, 'getPrerequisites']),
            new TwigFilter('perscom_course_qualifications', [PerscomCourseExtensionRuntime::class, 'getQualifications']),
            new TwigFilter('perscom_course_users', [PerscomCourseExtensionRuntime::class, 'getUsers']),
        ];
    }
}
