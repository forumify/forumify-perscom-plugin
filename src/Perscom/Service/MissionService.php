<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Calendar\Entity\CalendarEvent;
use Forumify\Calendar\Repository\CalendarEventRepository;
use Forumify\Core\Entity\Notification;
use Forumify\Core\Notification\NotificationService;
use Forumify\Core\Repository\ACLRepository;
use Forumify\Core\Repository\SettingRepository;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Notification\MissionCreatedNotificationType;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRepository;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Forumify\PerscomPlugin\Perscom\Repository\StatusRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MissionService
{
    public function __construct(
        private readonly MissionRepository $missionRepository,
        private readonly NotificationService $notificationService,
        private readonly ACLRepository $ACLRepository,
        private readonly SettingRepository $settingRepository,
        private readonly UserRepository $userRepository,
        private readonly CalendarEventRepository $calendarEventRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly StatusRepository $statusRepository,
    ) {
    }

    public function createCalendarEvent(Mission $mission): void
    {
        $calendar = $mission->getCalendar();
        if ($calendar === null) {
            return;
        }

        $event = $this->createOrUpdateCalendarEvent($mission);
        $mission->setCalendarEvent($event);
        $this->missionRepository->save($mission);
    }

    public function createOrUpdateCalendarEvent(Mission $mission): CalendarEvent
    {
        $event = $mission->getCalendarEvent() ?? new CalendarEvent();
        $event->setCalendar($mission->getCalendar());
        $event->setTitle($mission->getTitle());
        $event->setStart($mission->getStart());

        $missionLink = $this->urlGenerator->generate('perscom_missions_view', ['id' => $mission->getId()]);
        $content = "<p><a href='$missionLink' target='_blank'><i class='ph ph-arrow-square-out'></i> View mission</a></p>";
        $event->setContent($content);

        $this->calendarEventRepository->save($event);
        return $event;
    }

    public function sendNotification(Mission $mission): void
    {
        if (!$mission->isSendNotification()) {
            return;
        }

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

        $perscomUsers = $this->getActiveDutyPerscomUsersByForumifyUser($usersWithAccess);

        $recipients = [];
        foreach ($perscomUsers as $user) {
            $fUser = $user->getUser();
            if ($fUser !== null) {
                $recipients[] = $fUser;
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
     * @return array<PerscomUser>
     */
    private function getActiveDutyPerscomUsersByForumifyUser(array $users): array
    {
        $qb = $this
            ->perscomUserRepository
            ->createQueryBuilder('pu')
            ->where('pu.user IN (:users)')
            ->setParameter('users', $users)
        ;

        $enlistmentStatuses = $this->settingRepository->get('perscom.enlistment.status') ?? [];
        if (!empty($enlistmentStatuses)) {
            $statuses = $this->statusRepository->findBy(['id' => $enlistmentStatuses]);
            if (!empty($statuses)) {
                $qb
                    ->andWhere('pu.status NOT IN (:statuses)')
                    ->setParameter('statuses', $statuses)
                ;
            }
        }

        return $qb->getQuery()->getResult();
    }
}
