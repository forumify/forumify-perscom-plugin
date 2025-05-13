<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event\Listener;

use Forumify\Core\Entity\Notification;
use Forumify\Core\Entity\User;
use Forumify\Core\Notification\NotificationService;
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

        $userIds = array_column($event->records, 'user_id');
        $users = $this->perscomUserRepository->findByPerscomIds($userIds);
        foreach ($users as $user) {
            foreach ($event->records as $record) {
                if ($record['user_id'] === $user->getId()) {
                    $this->sendNotification($event->type, $user->getUser(), $record);
                }
            }
        }
    }

    private function sendNotification(string $type, User $user, array $data): void
    {
        // TODO: it'd be great if we could bulk fetch these, or add the includes to the original batchCreate request
        $userResource = $this->perscomFactory->getPerscom()->users();
        $resource = match ($type) {
            'service' => $userResource->service_records(...),
            'award' => $userResource->award_records(...),
            'combat' => $userResource->combat_records(...),
            'rank' => $userResource->rank_records(...),
            'assignment' => $userResource->assignment_records(...),
            'qualification' => $userResource->qualification_records(...),
            default => null,
        };
        if ($resource === null) {
            return;
        }

        $includes = $this->getIncludesForType($type);
        $data = $resource($data['user_id'])
            ->get($data['id'], $includes)
            ->json()['data'];

        if (isset($data['user'])) {
            // not needed for notification, remove to keep payload small.
            unset($data['user']);
        }

        $this->notificationService->sendNotification(new Notification(
            NewRecordNotificationType::TYPE,
            $user,
            [
                'type' => $type,
                'data' => $data,
            ]
        ));
    }

    private function getIncludesForType(string $type): array
    {
        return match ($type) {
            'award' => ['award'],
            'rank' => ['rank'],
            'assignment' => ['unit', 'position', 'specialty'],
            'qualification' => ['qualification'],
            default => [],
        };
    }
}
