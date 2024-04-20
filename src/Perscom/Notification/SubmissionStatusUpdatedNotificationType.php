<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Notification;

use Forumify\Core\Entity\Notification;
use Forumify\Core\Notification\AbstractEmailNotificationType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubmissionStatusUpdatedNotificationType extends AbstractEmailNotificationType
{
    public const TYPE = 'perscom_submission_status_updated';

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getTitle(Notification $notification): string
    {
        return $this->translator->trans('perscom.notification.submission_status_updated.title');
    }

    public function getDescription(Notification $notification): string
    {
        $context = $notification->getDeserializedContext();
        return $this->translator->trans('perscom.notification.submission_status_updated.description', [
            'form' => $context['form'],
            'status' => $context['status'],
        ]);
    }

    public function getImage(Notification $notification): string
    {
        return '';
    }

    public function getUrl(Notification $notification): string
    {
        $context = $notification->getDeserializedContext();
        return $this->urlGenerator->generate('perscom_operations_center', [
            'submission' => $context['submissionId']
        ]);
    }

    public function getEmailTemplate(Notification $notification): string
    {
        return '@ForumifyPerscomPlugin/emails/notifications/submission_status_updated.html.twig';
    }
}
