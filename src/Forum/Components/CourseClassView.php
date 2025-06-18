<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use DateTime;
use Forumify\Core\Security\VoterAttribute;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassInstructor;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassStudent;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Forumify\PerscomPlugin\Perscom\Entity\Record\QualificationRecord;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassInstructorRepository;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassStudentRepository;
use Forumify\PerscomPlugin\Perscom\Repository\CourseInstructorRepository;
use Forumify\PerscomPlugin\Perscom\Repository\RankRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('Perscom\\CourseClassView', '@ForumifyPerscomPlugin/frontend/components/course_class/class.html.twig')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class CourseClassView extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public CourseClass $class;

    public function __construct(
        private readonly PerscomUserService $perscomUserService,
        private readonly RankRepository $rankRepository,
        private readonly CourseInstructorRepository $instructorRepository,
        private readonly CourseClassStudentRepository $classStudentRepository,
        private readonly CourseClassInstructorRepository $classInstructorRepository,
    ) {
    }

    public function isSignupOpen(): bool
    {
        $now = new DateTime();
        return $this->class->getResult() === false
            && $now > $this->class->getSignupFrom()
            && $now < $this->class->getSignupUntil();
    }

    public function canSignUpAsStudent(): bool
    {
        $user = $this->perscomUserService->getLoggedInPerscomUser();
        if ($user === null) {
            // How did you even get here? lmao
            return false;
        }


        $qualificationIds = $user
            ->getQualificationRecords()
            ->map(fn (QualificationRecord $r) => $r->getQualification()->getPerscomId())
            ->toArray()
        ;

        $prerequisites = $this->class->getCourse()->getPrerequisites();
        foreach ($prerequisites as $prerequisiteId) {
            if (!in_array((int)$prerequisiteId, $qualificationIds, true)) {
                return false;
            }
        }

        $rankId = $this->class->getCourse()->getRankRequirement();
        if ($rankId === null) {
            return true;
        }

        /** @var Rank|null $rank */
        $rank = $this->rankRepository->findOneBy(['perscomId' => $rankId]);
        return $rank?->getPosition() >= $user->getRank()->getPosition();
    }

    public function isSignedUpAsStudent(): bool
    {
        $perscomUserId = $this->perscomUserService->getLoggedInPerscomUser()?->getPerscomId();
        if ($perscomUserId === null) {
            return false;
        }

        $student = $this->classStudentRepository->find(['perscomUserId' => $perscomUserId, 'class' => $this->class]);
        return $student !== null;
    }

    #[LiveAction]
    public function toggleStudent(): void
    {
        if (!$this->canSignUpAsStudent()) {
            return;
        }

        $perscomUserId = $this->perscomUserService->getLoggedInPerscomUser()?->getPerscomId();
        if ($perscomUserId === null) {
            return;
        }

        $student = $this->classStudentRepository->find(['perscomUserId' => $perscomUserId, 'class' => $this->class]);
        if ($student === null) {
            $student = new CourseClassStudent();
            $student->setClass($this->class);
            $student->setPerscomUserId($perscomUserId);
            $this->classStudentRepository->save($student);
        } else {
            $this->classStudentRepository->remove($student);
        }
    }

    #[LiveAction]
    public function registerInstructor(#[LiveArg] ?int $instructorId = null): void
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'permission' => 'signup_as_instructor',
            'entity' => $this->class->getCourse(),
        ]);

        $perscomUserId = $this->perscomUserService->getLoggedInPerscomUser()?->getPerscomId();
        if ($perscomUserId === null) {
            return;
        }

        $instructor = $this->classInstructorRepository->find([
            'perscomUserId' => $perscomUserId,
            'class' => $this->class,
        ]);

        if ($instructor !== null) {
            $this->classInstructorRepository->remove($instructor);
            return;
        }

        $instructorType = $instructorId === null ? null : $this->instructorRepository->find($instructorId);

        $cInstructor = new CourseClassInstructor();
        $cInstructor->setPerscomUserId($perscomUserId);
        $cInstructor->setClass($this->class);
        $cInstructor->setInstructor($instructorType);
        $this->classInstructorRepository->save($cInstructor);
    }

    #[LiveAction]
    public function removeStudent(#[LiveArg] int $perscomUserId): void
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $this->class->getCourse(),
            'permission' => 'manage_classes'
        ]);

        $student = $this->classStudentRepository->find([
            'perscomUserId' => $perscomUserId,
            'class' => $this->class,
        ]);

        if ($student !== null) {
            $this->classStudentRepository->remove($student);
        }
    }

    #[LiveAction]
    public function removeInstructor(#[LiveArg] int $perscomUserId): void
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $this->class->getCourse(),
            'permission' => 'manage_classes'
        ]);

        $instructor = $this->classInstructorRepository->find([
            'perscomUserId' => $perscomUserId,
            'class' => $this->class,
        ]);

        if ($instructor !== null) {
            $this->classInstructorRepository->remove($instructor);
        }
    }

    public function isSignedUpAsInstructor(): bool
    {
        $perscomUserId = $this->perscomUserService->getLoggedInPerscomUser()?->getPerscomId();
        if ($perscomUserId === null) {
            return false;
        }

        return $this->classInstructorRepository->find([
            'perscomUserId' => $perscomUserId,
            'class' => $this->class,
        ]) !== null;
    }

    public function getStudentSlots(): int
    {
        if (!$this->isSignupOpen()) {
            return 0;
        }

        $classSlots = $this->class->getStudentSlots();
        if ($classSlots === null) {
            return 3;
        }

        if ($classSlots === 0) {
            return 0;
        }
        return max(0, $classSlots - $this->class->getStudents()->count());
    }
}
