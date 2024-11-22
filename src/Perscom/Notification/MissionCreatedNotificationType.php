<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Notification;

use Forumify\Core\Entity\Notification;
use Forumify\Core\Notification\AbstractEmailNotificationType;

class MissionCreatedNotificationType extends AbstractEmailNotificationType
{
    public const TYPE = 'mission_created';

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getTitle(Notification $notification): string
    {
        return '';
    }

    public function getDescription(Notification $notification): string
    {
        return '';
    }

    public function getImage(Notification $notification): string
    {
        return '';
    }

    public function getUrl(Notification $notification): string
    {
        return '';
    }

    public function getEmailTemplate(Notification $notification): string
    {
        return '';
    }
}
