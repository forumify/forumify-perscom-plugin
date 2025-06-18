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
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Event\UserEnlistedEvent;
use Forumify\PerscomPlugin\Perscom\Repository\EnlistmentTopicRepository;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PerscomEnlistService
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly PerscomUserService $perscomUserService,
        private readonly SettingRepository $settingRepository,
        private readonly ForumRepository $forumRepository,
        private readonly CreateTopicService $createTopicService,
        private readonly EnlistmentTopicRepository $enlistmentTopicRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
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

    public function getEnlistmentForm(): ?array
    {
        $formId = $this->settingRepository->get('perscom.enlistment.form');

        return $this->perscomFactory->getPerscom()
            ->forms()
            ->get($formId, ['fields'])
            ->json('data') ?? [];
    }

    public function getCurrentEnlistment(int $submissionId): ?array
    {
        try {
            return $this->perscomFactory
                ->getPerscom()
                ->submissions()
                ->get($submissionId, ['statuses'])
                ->json('data');
        } catch (\Exception) {
            return null;
        }
    }

    public function enlist(Enlistment $enlistment): ?EnlistmentTopic
    {
        $perscomUser = $this->getOrCreatePerscomUser($enlistment);
        // TODO: move to entity listener?
        // $submission = $this->perscomFactory
        //    ->getPerscom()
        //    ->submissions()
        //    ->create([
        //        'form_id' => $this->getEnlistmentForm()['id'],
        //       'user_id' => $perscomUser['id'],
        //        ...$enlistment->additionalFormData,
        //    ])
        //    ->json('data');

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

    private function createEnlistmentTopic(PerscomUser $perscomUser, array $submission): ?EnlistmentTopic
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
        $enlistmentTopic = new EnlistmentTopic($submission['id'], $topic);
        $this->enlistmentTopicRepository->save($enlistmentTopic);

        return $enlistmentTopic;
    }

    private function formSubmissionToMarkdown(array $submission): string
    {
        $content = '';

        /** @var array $form */
        $form = $this->getEnlistmentForm();
        foreach ($form['fields'] as $field) {
            $label = $field['name'];
            $value = $submission[$field['key']] ?? '';

            $value = match ($field['type']) {
                'boolean' => $value ? 'Yes': 'No',
                'date' => (new \DateTime($value))->format('Y-m-d'),
                'datetime-local' => (new \DateTime($value))->format('Y-m-d H:i:s'),
                'select' => $field['options'][$value] ?? '',
                default => $value,
            };

            $content .= "<h5>$label</h5><p>$value</p>";
        }

        return $content;
    }
}
