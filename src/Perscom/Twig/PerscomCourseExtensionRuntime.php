<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Doctrine\Common\Collections\Collection;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassInstructor;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassStudent;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Forumify\PerscomPlugin\Perscom\Repository\QualificationRepository;
use Forumify\PerscomPlugin\Perscom\Repository\RankRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Twig\Extension\RuntimeExtensionInterface;

class PerscomCourseExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly PerscomUserService $userService,
        private readonly RankRepository $rankRepository,
        private readonly QualificationRepository $qualificationRepository,
        private readonly PerscomUserRepository $perscomUserRepository,
    ) {
    }

    public function getQualifications(array $ids): array
    {
        $qualifications = $this->qualificationRepository->findByPerscomIds($ids);
        return array_map((fn (Qualification $qual) => $qual->getName()), $qualifications);
    }

    public function getPrerequisites(Course $course): array
    {
        $prerequisites = [];

        $rankRequirementId = $course->getRankRequirement();
        if ($rankRequirementId !== null) {
            $rank = $this->rankRepository->findOneByPerscomId($rankRequirementId);
            if ($rank !== null) {
                $prerequisites[] = $rank->getName();
            }
        }

        $qualifications = $this->getQualifications($course->getPrerequisites());
        foreach ($qualifications as $qualification) {
            $prerequisites[] = $qualification;
        }

        return $prerequisites;
    }

    /**
     * @param Collection<int, CourseClassStudent|CourseClassInstructor> $users
     * @return array<int, array{ user: PerscomUser, courseUser: CourseClassStudent|CourseClassInstructor }>
     */
    public function getUsers(Collection $users): array
    {
        if ($users->isEmpty()) {
            return [];
        }

        $userIds = $users
            ->map(fn (CourseClassInstructor|CourseClassStudent $user) => $user->getPerscomUserId())
            ->toArray()
        ;
        $courseUsers = array_combine($userIds, $users->toArray());

        $users = $this->perscomUserRepository->findByPerscomIds($userIds);
        $this->userService->sortPerscomUsers($users);

        $return = [];
        foreach ($users as $user) {
            $return[$user->getPerscomId()] = [
                'courseUser' => $courseUsers[$user->getPerscomId()],
                'user' => $user,
            ];
        }
        return $return;
    }
}
