<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use DateTime;
use Forumify\Core\Security\VoterAttribute;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('Perscom\\CourseClassSignup', '@ForumifyPerscomPlugin/frontend/components/course_class_signup.html.twig')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class CourseClassSignup extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public CourseClass $class;

    private ?array $perscomUser = null;

    public function __construct(
        private readonly PerscomUserService $perscomUserService,
        private readonly PerscomFactory $perscomFactory,
        private readonly CourseClassRepository $courseClassRepository,
        private readonly Security $security,
    ) {
    }

    public function isSignupOpen(): bool
    {
        $now = new DateTime();
        return $this->class->getResult() === null
            && $now > $this->class->getSignupFrom()
            && $now < $this->class->getSignupUntil();
    }

    public function canSignUpAsStudent(): bool
    {
        $perscomUserId = $this->perscomUserService->getLoggedInPerscomUser()['id'] ?? null;
        if ($perscomUserId === null) {
            // How did you even get here? lmao
            return false;
        }

        $perscom = $this->perscomFactory->getPerscom();

        try {
            $user = $perscom
                ->users()
                ->get($perscomUserId, ['rank', 'qualification_records'])
                ->json('data');
        } catch (\Exception) {
            return false;
        }

        $prerequisites = $this->class->getCourse()->getPrerequisites();
        $qualifications = array_column($user['qualification_records'], 'qualification_id');
        foreach ($prerequisites as $prerequisiteId) {
            if (!in_array((int)$prerequisiteId, $qualifications, true)) {
                return false;
            }
        }

        $rankId = $this->class->getCourse()->getRankRequirement();
        if ($rankId === null) {
            return true;
        }

        try {
            $rankRequirement = $perscom
                ->ranks()
                ->get($rankId)
                ->json('data');
        } catch (\Exception) {
            return false;
        }

        return $rankRequirement['order'] > $user['rank']['order'];
    }

    public function isSignedUpAsStudent(): bool
    {
        $perscomUserId = $this->perscomUserService->getLoggedInPerscomUser()['id'] ?? null;
        if ($perscomUserId === null) {
            return false;
        }

        return in_array($perscomUserId, $this->class->getStudents(), true);
    }

    #[LiveAction]
    public function toggleStudent(): ?Response
    {
        if (!$this->canSignUpAsStudent()) {
            return null;
        }

        $perscomUserId = $this->perscomUserService->getLoggedInPerscomUser()['id'] ?? null;
        if ($this->isSignedUpAsStudent()) {
            $this->class->setStudents(array_filter($this->class->getStudents(), static fn (int $id) => $id !== $perscomUserId));
        } else {
            $this->class->addStudent($perscomUserId);
        }

        $this->courseClassRepository->save($this->class);
        return $this->redirectToRoute('perscom_course_class_view', ['id' => $this->class->getId()]);
    }

    public function isSignedUpAsInstructor(): bool
    {
        $perscomUserId = $this->perscomUserService->getLoggedInPerscomUser()['id'] ?? null;
        if ($perscomUserId === null) {
            return false;
        }

        return in_array($perscomUserId, $this->class->getInstructors(), true);
    }

    #[LiveAction]
    public function toggleInstructor(): ?Response
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'permission' => 'signup_as_instructor',
            'entity' => $this->class->getCourse(),
        ]);

        $perscomUserId = $this->perscomUserService->getLoggedInPerscomUser()['id'] ?? null;
        if ($this->isSignedUpAsInstructor()) {
            $this->class->setInstructors(array_filter($this->class->getInstructors(), static fn (int $id) => $id !== $perscomUserId));
        } else {
            $this->class->addInstructor($perscomUserId);
        }

        $this->courseClassRepository->save($this->class);
        return $this->redirectToRoute('perscom_course_class_view', ['id' => $this->class->getId()]);
    }
}
