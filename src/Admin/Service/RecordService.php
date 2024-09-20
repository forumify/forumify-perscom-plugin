<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Service;

use Forumify\Core\Entity\Notification;
use Forumify\Core\Entity\User;
use Forumify\Core\Notification\NotificationService;
use Forumify\Core\Repository\UserRepository;
use Forumify\Core\Twig\Extension\MenuRuntime;
use Forumify\PerscomPlugin\Perscom\Notification\NewRecordNotificationType;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Contracts\Cache\CacheInterface;

class RecordService
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly NotificationService $notificationService,
        private readonly UserRepository $userRepository,
        private readonly CacheInterface $cache,
    ) {
    }

    public function createRecord(string $type, array $data): void
    {
        $sendNotification = $data['sendNotification'] ?? false;
        unset($data['sendNotification']);

        $userIds = $data['users'] ?? [];
        unset($data['users']);

        $userResource = $this->perscomFactory->getPerscom()->users();
        $recordResource = match ($type) {
            'service' => $userResource->service_records(...),
            'award' => $userResource->award_records(...),
            'combat' => $userResource->combat_records(...),
            'rank' => $userResource->rank_records(...),
            'assignment' => $userResource->assignment_records(...),
            'qualification' => $userResource->qualification_records(...),
        };

        foreach ($userIds as $userId) {
            $data['user_id'] = (int)$userId;
            $response = $recordResource($data['user_id'])->create($data)->json()['data'];
            if ($sendNotification) {
                $this->sendNotification($recordResource, $type, $response);
            }

            if ($type === 'assignment') {
                $user = $this->getUserByEmail($data);
                $this->cache->delete(MenuRuntime::createMenuCacheKey($user));
            }
        }
    }

    private function sendNotification(callable $resource, string $type, array $data): void
    {
        $user = $this->getUserByEmail($data);
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

    private function getUserByEmail(array $data): ?User
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

        return $this->userRepository->findOneBy(['email' => $userEmail]);
    }
}
