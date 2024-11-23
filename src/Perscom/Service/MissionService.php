<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Entity\Notification;
use Forumify\Core\Entity\Role;
use Forumify\Core\Entity\User;
use Forumify\Core\Notification\NotificationService;
use Forumify\Core\Repository\ACLRepository;
use Forumify\Core\Repository\SettingRepository;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\Notification\MissionCreatedNotificationType;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRepository;
use Perscom\Data\FilterObject;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class MissionService
{
    public function __construct(
        private readonly MissionRepository $missionRepository,
        private readonly NotificationService $notificationService,
        private readonly ACLRepository $ACLRepository,
        private readonly PerscomFactory $perscomFactory,
        private readonly SettingRepository $settingRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function createOrUpdate(Mission $mission, bool $isNew): void
    {
        $this->missionRepository->save($mission);

        if ($isNew && $mission->isSendNotification()) {
            $this->sendNotification($mission);
        }
    }

    private function sendNotification(Mission $mission): void
    {
        $recipients = $this->getRecipientsForMission($mission);
        foreach ($recipients as $recipient) {
            $notification = new Notification(MissionCreatedNotificationType::TYPE, $recipient, [
                'mission' => $mission,
            ]);
            $this->notificationService->sendNotification($notification);
        }
    }

    /**
     * @param Mission $mission
     * @return array
     */
    private function getRecipientsForMission(Mission $mission): array
    {
        $usersWithAccess = $this->getForumifyUsersWithMissionAccess($mission);
        if (empty($usersWithAccess)) {
            return [];
        }

        try {
            $perscomUsers = $this->getActiveDutyPerscomUsersByForumifyUser($usersWithAccess);
            $perscomEmails = array_map('strtolower', array_column($perscomUsers, 'email'));
        } catch (\Exception) {
            return [];
        }

        $recipients = [];
        foreach ($usersWithAccess as $user) {
            if (in_array(strtolower($user->getEmail()), $perscomEmails, true)) {
                $recipients[] = $user;
            }
        }

        return $recipients;
    }

    private function getForumifyUsersWithMissionAccess(Mission $mission): array
    {
        $operation = $mission->getOperation();
        $acl = $this->ACLRepository->findOneByEntityAndPermission($operation, 'view_missions');
        if ($acl === null) {
            return [];
        }

        $usersWithAccess = [];
        foreach ($acl->getRoles() as $role) {
            if ($role->getSlug() === 'user') {
                return $this->userRepository->findAll();
            }

            foreach ($role->getUsers() as $user) {
                $usersWithAccess[$user->getId()] = $user;
            }
        }
        return $usersWithAccess;
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \JsonException
     */
    private function getActiveDutyPerscomUsersByForumifyUser(array $users): array
    {
        $emails = array_map(fn (User $user) => $user->getEmail(), $users);
        $filters = [new FilterObject('email', 'in', $emails)];

        $enlistmentStatuses = $this->settingRepository->get('perscom.enlistment.status') ?? [];
        if (!empty($enlistmentStatuses)) {
            $filters[] = new FilterObject('status_id', 'not in', $enlistmentStatuses);
        }

        return $this->perscomFactory->getPerscom()
            ->users()
            ->search(filter: $filters, limit: 9999)
            ->json('data');
    }
}
