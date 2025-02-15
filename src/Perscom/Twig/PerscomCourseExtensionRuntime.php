<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Exception;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Perscom\Data\FilterObject;
use Twig\Extension\RuntimeExtensionInterface;

class PerscomCourseExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly PerscomUserService $userService,
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
                ->search(
                    filter: new FilterObject('id', 'in', $ids),
                    include: [
                        'rank',
                        'rank.image',
                        'position',
                        'specialty',
                    ])
                ->json('data');
        } catch (Exception) {
            return [];
        }
        $users = array_combine(array_column($users, 'id'), $users);
        $this->userService->sortUsers($users);

        foreach ($users as $k => $user) {
            $users[$k] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'rankImage' => !empty($user['rank']['image']) ? $user['rank']['image']['image_url'] : null,
            ];
        }
        return $users;
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
        return $instructors[$instructorId]['attended'] ?? false;
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
