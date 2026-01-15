<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Entity\User;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Forum\Form\Enlistment;
use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Forumify\PerscomPlugin\Perscom\Entity\FormSubmission;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Event\UserEnlistedEvent;
use Forumify\PerscomPlugin\Perscom\Repository\FormRepository;
use Forumify\PerscomPlugin\Perscom\Repository\FormSubmissionRepository;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PerscomEnlistService
{
    public function __construct(
        private readonly PerscomUserService $perscomUserService,
        private readonly SettingRepository $settingRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly FormRepository $formRepository,
        private readonly FormSubmissionRepository $formSubmissionRepository,
    ) {
    }

    public function canEnlist(?User $user = null): bool
    {
        $perscomUser = $user === null
            ? $this->perscomUserService->getLoggedInPerscomUser()
            : $this->perscomUserService->getPerscomUser($user);

        if ($perscomUser === null) {
            return true;
        }

        $allowedEnlistmentStatuses = $this->settingRepository->get('perscom.enlistment.status') ?? [];
        $statusId = $perscomUser->getStatus()?->getId();
        return $statusId === null || in_array($statusId, $allowedEnlistmentStatuses, true);
    }

    public function getEnlistmentForm(): ?Form
    {
        $formId = $this->settingRepository->get('perscom.enlistment.form');
        if ($formId === null) {
            return null;
        }

        return $this->formRepository->find($formId);
    }

    public function enlist(Enlistment $enlistment): PerscomUser
    {
        $perscomUser = $this->perscomUserService->getLoggedInPerscomUser()
            ?? $this->perscomUserService->createUser($enlistment);

        $submission = new FormSubmission();
        $submission->setForm($this->getEnlistmentForm());
        $submission->setUser($perscomUser);
        $submission->setData($enlistment->additionalFormData);
        $this->formSubmissionRepository->save($submission);

        $this->eventDispatcher->dispatch(new UserEnlistedEvent($perscomUser, $submission));
        return $perscomUser;
    }
}
