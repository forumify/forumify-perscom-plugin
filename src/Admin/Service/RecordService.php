<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Service;

use Exception;
use Forumify\Core\Entity\Notification;
use Forumify\Core\Entity\User;
use Forumify\Core\Notification\NotificationService;
use Forumify\Core\Repository\UserRepository;
use Forumify\Core\Twig\Extension\MenuRuntime;
use Forumify\PerscomPlugin\Perscom\Exception\PerscomException;
use Forumify\PerscomPlugin\Perscom\Exception\PerscomUserNotFoundException;
use Forumify\PerscomPlugin\Perscom\Notification\NewRecordNotificationType;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Forumify\PerscomPlugin\Perscom\Service\SyncUserService;
use Perscom\Data\FilterObject;
use Perscom\Data\ResourceObject;
use Symfony\Contracts\Cache\CacheInterface;

class RecordService
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly PerscomUserService $perscomUserService,
        private readonly NotificationService $notificationService,
        private readonly UserRepository $userRepository,
        private readonly CacheInterface $cache,
        private readonly SyncUserService $syncUserService,
    ) {
    }

    /**
     * @throws PerscomException
     */
    public function createRecord(string $type, array $data): void
    {
        $sendNotification = $data['sendNotification'] ?? false;
        unset($data['sendNotification']);

        $perscom = $this->perscomFactory->getPerscom();
        $recordResource = match ($type) {
            'service' => $perscom->serviceRecords(),
            'award' => $perscom->awardRecords(),
            'combat' => $perscom->combatRecords(),
            'rank' => $perscom->rankRecords(),
            'assignment' => $perscom->assignmentRecords(),
            'qualification' => $perscom->qualificationRecords()
        };

        $records = $this->dataToResourceObjects($data);
        try {
            $responses = $recordResource->batchCreate($records)->json('data');
            $userIds = array_column($responses, 'user_id');
            $users = $perscom
                ->users()
                ->search(filter: new FilterObject('id', 'in', $userIds))
                ->json('data');
        } catch (Exception $ex) {
            throw new PerscomException($ex->getMessage(), 0, $ex);
        }

        $users = array_combine(array_column($users, 'id'), array_column($users, 'email'));
        foreach ($responses as $response) {
            $email = $users[$response['user_id']] ?? null;
            if ($email === null) {
                continue;
            }

            $user = $this->userRepository->findOneBy(['email' => $email]);
            if ($user === null) {
                continue;
            }

            if ($sendNotification) {
                $this->sendNotification($type, $user, $response);
            }

            if ($type === 'assignment') {
                $this->cache->delete(MenuRuntime::createMenuCacheKey($user));
            }
        }

        foreach ($userIds as $userId) {
            $this->syncUserService->syncFromPerscom($userId);
        }
    }

    /**
     * @return array<ResourceObject>
     * @throws PerscomUserNotFoundException
     */
    private function dataToResourceObjects(array $data): array
    {
        $author = $this->perscomUserService->getLoggedInPerscomUser();
        if ($author === null || empty($author['id'])) {
            throw new PerscomUserNotFoundException();
        }

        $userIds = $data['users'] ?? [];
        unset($data['users']);

        return array_map(static fn (int|string $userId) => new ResourceObject(null, [
            'user_id' => (int)$userId,
            'author_id' => $author['id'],
            ...$data,
        ]), $userIds);
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
        };
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
