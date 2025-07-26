<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\EventSubscriber;

use Forumify\Core\Repository\SettingRepository;
use Forumify\Forum\Entity\Topic;
use Forumify\Forum\Form\TopicData;
use Forumify\Forum\Repository\ForumRepository;
use Forumify\Forum\Service\CreateTopicService;
use Forumify\PerscomPlugin\Perscom\Entity\FormSubmission;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Event\UserEnlistedEvent;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class EnlistListener
{
    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly ForumRepository $forumRepository,
        private readonly CreateTopicService $createTopicService,
        private readonly PerscomUserRepository $userRepository,
    ) {
    }

    public function __invoke(UserEnlistedEvent $event): void
    {
        $user = $event->perscomUser;
        $topic = $this->createEnlistmentTopic($user, $event->submission);
        if ($topic === null) {
            return;
        }

        $user->setEnlistmentTopic($topic);
        $this->userRepository->save($user);
    }

    private function createEnlistmentTopic(PerscomUser $perscomUser, FormSubmission $submission): ?Topic
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
        return $this->createTopicService->createTopic($forum, $newTopic);
    }

    private function formSubmissionToMarkdown(FormSubmission $submission): string
    {
        $content = '';

        $data = $submission->getData();
        foreach ($submission->getForm()->getFields() as $field) {
            $label = $field->getLabel();
            $value = $data[$field->getKey()] ?? '';

            $value = match ($field->getType()) {
                'boolean' => $value ? 'Yes' : 'No',
                'date' => (new \DateTime($value))->format('Y-m-d'),
                'datetime-local' => (new \DateTime($value))->format('Y-m-d H:i:s'),
                'select' => array_find_key($field->getOptions(), fn($v) => $v === $value),
                default => $value,
            };

            $content .= "<h5>$label</h5><p>$value</p>";
        }

        return $content;
    }
}
