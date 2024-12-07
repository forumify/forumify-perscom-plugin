<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Exception;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\FilterObject;
use Twig\Extension\RuntimeExtensionInterface;

class PerscomCourseExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
    ) {
    }

    public function getQualifications(array $ids): array
    {
        try {
            $qualifications = $this->perscomFactory
                ->getPerscom()
                ->qualifications()
                ->search(filter: new FilterObject('id', 'in', $ids))
                ->json('data')
            ;
        } catch (Exception) {
            return [];
        }

        return array_column($qualifications, 'name');
    }

    public function getPrerequisites(Course $course): array
    {
        $prerequisites = [];
        if ($course->getRankRequirement() !== null) {
            $rank = $this->getRank($course->getRankRequirement());
            if (isset($rank['name'])) {
                $prerequisites[] = $rank['name'];
            }
        }

        $qualifications = $this->getQualifications($course->getPrerequisites());
        foreach ($qualifications as $qualification) {
            $prerequisites[] = $qualification;
        }

        return $prerequisites;
    }

    public function getUsers(array $ids): array
    {
        try {
            $users = $this->perscomFactory
                ->getPerscom()
                ->users()
                ->search(filter: new FilterObject('id', 'in', $ids))
                ->json('data')
            ;
        } catch (Exception) {
            return [];
        }

        return array_combine(array_column($users, 'id'), array_column($users, 'name'));
    }

    private function getRank(int $id): ?array
    {
        try {
            return $this->perscomFactory->getPerscom()->ranks()->get($id)->json('data');
        } catch (Exception) {
            return null;
        }
    }

    public function instructorAttended(CourseClass $class, int $instructorId): ?bool
    {
        if ($class->getResult() === null) {
            return null;
        }

        $instructors = $class->getResult()->getResult()['instructors'] ?? null;
        return $instructors[$instructorId] ?? false;
    }

    public function studentResult(CourseClass $class, int $studentId): ?string
    {
        if ($class->getResult() === null) {
            return null;
        }

        $students = $class->getResult()->getResult()['students'] ?? null;
        return $students[$studentId]['result'] ?? null;
    }
}
