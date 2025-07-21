<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use DateTime;
use Forumify\Core\Security\VoterAttribute;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassInstructor;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassStudent;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassInstructorRepository;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassStudentRepository;
use Forumify\PerscomPlugin\Perscom\Repository\CourseInstructorRepository;
use Forumify\PerscomPlugin\Perscom\Repository\QualificationRecordRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[AsLiveComponent('Perscom\\CourseClassView', '@ForumifyPerscomPlugin/frontend/components/course_class/class.html.twig')]
class CourseClassView extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public CourseClass $class;

    public function __construct(
        private readonly PerscomUserService $perscomUserService,
        private readonly CourseInstructorRepository $instructorRepository,
        private readonly CourseClassStudentRepository $classStudentRepository,
        private readonly CourseClassInstructorRepository $classInstructorRepository,
        private readonly QualificationRecordRepository $qualificationRecordRepository,
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
            return false;
        }

        $qualifications = $this->qualificationRecordRepository
            ->createQueryBuilder('qr')
            ->select('DISTINCT IDENTITY(qr.qualification)')
            ->where('qr.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleColumnResult()
        ;

        $prerequisites = $this->class->getCourse()->getPrerequisites();
        foreach ($prerequisites as $prerequisiteId) {
            if (!in_array((int)$prerequisiteId, $qualifications, true)) {
                return false;
            }
        }

        $minimumRank = $this->class->getCourse()->getMinimumRank();
        if ($minimumRank === null) {
            return true;
        }

        return $minimumRank->getPosition() >= $user->getRank()->getPosition();
    }

    public function isSignedUpAsStudent(): bool
    {
        $user = $this->perscomUserService->getLoggedInPerscomUser();
        if ($user === null) {
            return false;
        }

        return $this->classStudentRepository->count([
            'class' => $this->class,
            'user' => $user,
        ]) > 0;
    }

    #[LiveAction]
    public function toggleStudent(): void
    {
        if (!$this->canSignUpAsStudent()) {
            return;
        }

        $user = $this->perscomUserService->getLoggedInPerscomUser();
        if ($user === null) {
            return;
        }

        $student = $this->classStudentRepository->findOneBy(['user' => $user, 'class' => $this->class]);
        if ($student === null) {
            $student = new CourseClassStudent();
            $student->setClass($this->class);
            $student->setUser($user);
            $this->classStudentRepository->save($student);
        } else {
            $this->classStudentRepository->remove($student);
        }
    }

    #[LiveAction]
    public function registerInstructor(#[LiveArg] ?int $instructorId = null): void
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $this->class->getCourse(),
            'permission' => 'signup_as_instructor',
        ]);

        $user = $this->perscomUserService->getLoggedInPerscomUser();
        if ($user === null) {
            return;
        }

        $instructor = $this->classInstructorRepository->findOneBy([
            'class' => $this->class,
            'user' => $user,
        ]);

        if ($instructor !== null) {
            $this->classInstructorRepository->remove($instructor);
            return;
        }

        $instructorType = $instructorId === null ? null : $this->instructorRepository->find($instructorId);

        $cInstructor = new CourseClassInstructor();
        $cInstructor->setUser($user);
        $cInstructor->setClass($this->class);
        $cInstructor->setInstructor($instructorType);
        $this->classInstructorRepository->save($cInstructor);
    }

    #[LiveAction]
    public function removeStudent(#[LiveArg] int $userId): void
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $this->class->getCourse(),
            'permission' => 'manage_classes',
        ]);

        $student = $this->classStudentRepository->findOneBy([
            'class' => $this->class,
            'user' => $userId,
        ]);

        if ($student !== null) {
            $this->classStudentRepository->remove($student);
        }
    }

    #[LiveAction]
    public function removeInstructor(#[LiveArg] int $userId): void
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $this->class->getCourse(),
            'permission' => 'manage_classes',
        ]);

        $instructor = $this->classInstructorRepository->findOneBy([
            'class' => $this->class,
            'user' => $userId,
        ]);

        if ($instructor !== null) {
            $this->classInstructorRepository->remove($instructor);
        }
    }

    public function isSignedUpAsInstructor(): bool
    {
        $user = $this->perscomUserService->getLoggedInPerscomUser();
        if ($user === null) {
            return false;
        }

        return $this->classInstructorRepository->count([
            'class' => $this->class,
            'user' => $user,
        ]) > 0;
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
