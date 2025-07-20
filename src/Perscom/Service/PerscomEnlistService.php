<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Entity\User;
use Forumify\Core\Repository\SettingRepository;
use Forumify\Forum\Form\TopicData;
use Forumify\Forum\Repository\ForumRepository;
use Forumify\Forum\Service\CreateTopicService;
use Forumify\PerscomPlugin\Perscom\Entity\EnlistmentTopic;
use Forumify\PerscomPlugin\Forum\Form\Enlistment;
use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Forumify\PerscomPlugin\Perscom\Entity\FormSubmission;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Event\UserEnlistedEvent;
use Forumify\PerscomPlugin\Perscom\Repository\EnlistmentTopicRepository;
use Forumify\PerscomPlugin\Perscom\Repository\FormRepository;
use Forumify\PerscomPlugin\Perscom\Repository\FormSubmissionRepository;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PerscomEnlistService
{
    public function __construct(
        private readonly PerscomUserService $perscomUserService,
        private readonly SettingRepository $settingRepository,
        private readonly ForumRepository $forumRepository,
        private readonly CreateTopicService $createTopicService,
        private readonly EnlistmentTopicRepository $enlistmentTopicRepository,
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
        $statusId = $perscomUser->getStatus()?->getPerscomId();
        return $statusId === null || in_array($statusId, $allowedEnlistmentStatuses, true);
    }

    public function getEnlistmentForm(): ?Form
    {
        $formId = $this->settingRepository->get('perscom.enlistment.form');
        if ($formId === null) {
            return null;
        }

        return $this->formRepository->findOneByPerscomId($formId);
    }

    public function getCurrentEnlistment(int $submissionId): ?FormSubmission
    {
        return $this->formSubmissionRepository->find($submissionId);
    }

    public function enlist(Enlistment $enlistment): ?EnlistmentTopic
    {
        $perscomUser = $this->getOrCreatePerscomUser($enlistment);

        $submission = new FormSubmission();
        $submission->setForm($this->getEnlistmentForm());
        $submission->setUser($perscomUser);
        $submission->setData($enlistment->additionalFormData);
        $this->formSubmissionRepository->save($submission);

        $this->eventDispatcher->dispatch(new UserEnlistedEvent($perscomUser, $submission));
        return $this->createEnlistmentTopic($perscomUser, $submission);
    }

    private function getOrCreatePerscomUser(Enlistment $enlistment): PerscomUser
    {
        $perscomUser = $this->perscomUserService->getLoggedInPerscomUser();

        return $perscomUser ?? $this->perscomUserService->createUser(
            $enlistment->firstName,
            $enlistment->lastName,
        );
    }

    private function createEnlistmentTopic(PerscomUser $perscomUser, FormSubmission $submission): ?EnlistmentTopic
    {
        $forumId = $this->settingRepository->get('perscom.enlistment.forum');
        if (!$forumId) {
            return null;
        }

        $forum = $this->forumRepository->find($forumId);
        if ($forum === null) {
            return null;
        }

        $newTopic = new TopicData();
        $newTopic->setTitle("New enlistment from \"{$perscomUser->getName()}\"");
        $newTopic->setContent($this->formSubmissionToMarkdown($submission));

        $topic = $this->createTopicService->createTopic($forum, $newTopic);
        $enlistmentTopic = new EnlistmentTopic($submission->getId(), $topic);
        $this->enlistmentTopicRepository->save($enlistmentTopic);

        return $enlistmentTopic;
    }

    private function formSubmissionToMarkdown(FormSubmission $submission): string
    {
        $content = '';

        $form = $this->getEnlistmentForm();
        $data = $submission->getData();
        foreach ($form->getFields() as $field) {
            $label = $field->getLabel();
            $value = $data[$field->getKey()] ?? '';

            $value = match ($field->getType()) {
                'boolean' => $value ? 'Yes': 'No',
                'date' => (new \DateTime($value))->format('Y-m-d'),
                'datetime-local' => (new \DateTime($value))->format('Y-m-d H:i:s'),
                'select' => array_find_key($field->getOptions(), fn ($v) => $v === $value),
                default => $value,
            };

            $content .= "<h5>$label</h5><p>$value</p>";
        }

        return $content;
    }
}
