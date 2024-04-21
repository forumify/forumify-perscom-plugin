<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Service;

use Forumify\Core\Entity\Notification;
use Forumify\Core\Notification\NotificationService;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Admin\Form\StatusRecord;
use Forumify\PerscomPlugin\Perscom\Notification\SubmissionStatusUpdatedNotificationType;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\ResourceObject;

class SubmissionStatusUpdateService
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly UserRepository $userRepository,
        private readonly NotificationService $notificationService
    ) {
    }

    public function createStatusRecord(StatusRecord $statusRecord): void
    {
        $resource = new ResourceObject($statusRecord->status, ['text' => $statusRecord->text]);
        $this->perscomFactory
            ->getPerscom()
            ->submissions()
            ->statuses($statusRecord->submission['id'])
            ->attach($resource);

        if ($statusRecord->sendNotification) {
            $this->sendNotification($statusRecord);
        }
    }

    private function sendNotification(StatusRecord $statusRecord): void
    {
        $recipientEmail = $statusRecord->submission['user']['email'];
        $user = $this->userRepository->findOneBy(['email' => $recipientEmail]);
        if ($user === null) {
            return;
        }

        $status = $this->perscomFactory
            ->getPerscom()
            ->statuses()
            ->get($statusRecord->status)
            ->json()['data'] ?? null;

        if ($status === null) {
            return;
        }

        $this->notificationService->sendNotification(new Notification(
            SubmissionStatusUpdatedNotificationType::TYPE,
            $user,
            [
                'form' => $statusRecord->submission['form']['name'],
                'status' => $status['name'],
                'text' => $statusRecord->text,
                'submissionId' => $statusRecord->submission['id'],
            ]
        ));
    }
}
