<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Service;

use Forumify\Core\Entity\Notification;
use Forumify\Core\Notification\NotificationService;
use Forumify\PerscomPlugin\Perscom\Entity\FormSubmission;
use Forumify\PerscomPlugin\Perscom\Notification\SubmissionStatusUpdatedNotificationType;
use Forumify\PerscomPlugin\Perscom\Repository\FormSubmissionRepository;

class SubmissionStatusUpdateService
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly FormSubmissionRepository $formSubmissionRepository,
    ) {
    }

    public function createStatusRecord(FormSubmission $submission, array $statusRecord): void
    {
        $submission->setStatus($statusRecord['status']);
        $submission->setStatusReason($statusRecord['reason'] ?? '');
        $this->formSubmissionRepository->save($submission);

        if ($statusRecord['sendNotification'] ?? false) {
            $this->sendNotification($submission);
        }
    }

    private function sendNotification(FormSubmission $submission): void
    {
        $user = $submission->getUser()->getUser();
        $status = $submission->getStatus();
        if ($status === null || $user === null) {
            return;
        }

        $this->notificationService->sendNotification(new Notification(
            SubmissionStatusUpdatedNotificationType::TYPE,
            $user,
            [
                'form' => $submission->getForm()->getName(),
                'status' => $status->getName(),
                'submissionId' => $submission->getId(),
                'text' => $submission->getStatusReason(),
            ]
        ));
    }
}
