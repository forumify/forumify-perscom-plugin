<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event\Listener;

use Forumify\Core\Entity\Notification;
use Forumify\Core\Notification\NotificationService;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RecordInterface;
use Forumify\PerscomPlugin\Perscom\Event\RecordsCreatedEvent;
use Forumify\PerscomPlugin\Perscom\Notification\NewRecordNotificationType;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class SendRecordNotificationListener
{
    public function __construct(
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly PerscomFactory $perscomFactory,
        private readonly NotificationService $notificationService,
    ) {
    }

    public function __invoke(RecordsCreatedEvent $event): void
    {
        if (!$event->sendNotification) {
            return;
        }

        foreach ($event->records as $record) {
            $this->sendNotification($record);
        }
    }

    private function sendNotification(RecordInterface $record): void
    {
        $user = $record->getUser()->getUser();
        if ($user === null) {
            return;
        }

        // FIXME: probably just use generic notification, we CANNOT remove the old notification type for a while...
        $this->notificationService->sendNotification(new Notification(
            NewRecordNotificationType::TYPE,
            $user,
            [
                'type' => $type,
                'data' => $data,
            ]
        ));
    }
}
