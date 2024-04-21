<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Service;

use Forumify\Core\Entity\Notification;
use Forumify\Core\Notification\NotificationService;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\Notification\NewRecordNotificationType;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;

class RecordService
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly NotificationService $notificationService,
        private readonly UserRepository $userRepository,
    ) { }

    public function createRecord(string $type, array $data): void
    {
        $sendNotification = $data['sendNotification'] ?? false;
        unset($data['sendNotification']);

        $userResource = $this->perscomFactory->getPerscom()->users();
        $recordResource = match ($type) {
            'service' => $userResource->service_records(...),
            'award' => $userResource->award_records(...),
            'combat' => $userResource->combat_records(...),
            'rank' => $userResource->rank_records(...),
            'assignment' => $userResource->assignment_records(...),
            'qualification' => $userResource->qualification_records(...),
        };

        $data = $recordResource($data['user_id'])->create($data)->json()['data'];
        if ($sendNotification) {
            $this->sendNotification($recordResource, $type, $data);
        }
    }

    private function sendNotification(callable $resource, string $type, array $data): void
    {
        $userEmail = $data['user']['email'] ?? null;
        if (empty($userEmail)) {
            // some records include the user email, some don't
            $userEmail = $this->perscomFactory
                ->getPerscom()
                ->users()
                ->get($data['user_id'])
                ->json()['data']['email'] ?? null;
        }

        $user = $this->userRepository->findOneBy(['email' => $userEmail]);
        if ($user === null) {
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
