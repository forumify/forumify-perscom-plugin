<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Doctrine\Common\Collections\Collection;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassInstructor;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassStudent;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
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
     * @param Collection<int, CourseClassStudent|CourseClassInstructor> $classUsers
     * @return array<int, array{ user: PerscomUser, courseUser: CourseClassStudent|CourseClassInstructor }>
     */
    public function getUsers(Collection $classUsers): array
    {
        if ($classUsers->isEmpty()) {
            return [];
        }

        $userIds = $classUsers
            ->map(fn (CourseClassInstructor|CourseClassStudent $user) => $user->getUser()->getId())
            ->toArray()
        ;
        $courseUsers = array_combine($userIds, $classUsers->toArray());
        $perscomUsers = array_combine($userIds, $classUsers->map((fn ($user) => $user->getUser()))->toArray());

        $this->userService->sortPerscomUsers($perscomUsers);

        $return = [];
        foreach ($perscomUsers as $user) {
            $return[$user->getId()] = [
                'courseUser' => $courseUsers[$user->getId()],
                'user' => $user,
            ];
        }
        return $return;
    }
}
