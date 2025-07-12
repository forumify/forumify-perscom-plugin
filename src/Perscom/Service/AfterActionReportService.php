<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Entity\Notification;
use Forumify\Core\Notification\GenericEmailNotificationType;
use Forumify\Core\Notification\GenericNotificationType;
use Forumify\Core\Notification\NotificationService;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\AfterActionReport;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Forumify\PerscomPlugin\Perscom\Exception\AfterActionReportAlreadyExistsException;
use Forumify\PerscomPlugin\Perscom\Repository\AfterActionReportRepository;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Forumify\PerscomPlugin\Perscom\Repository\StatusRepository;
use Forumify\PerscomPlugin\Perscom\Repository\UnitRepository;
use JsonException;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AfterActionReportService
{
    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly AfterActionReportRepository $afterActionReportRepository,
        private readonly RecordService $recordService,
        private readonly PerscomUserService $userService,
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly NotificationService $notificationService,
        private readonly Packages $packages,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UnitRepository $unitRepository,
        private readonly StatusRepository $statusRepository,
    ) {
    }

    /**
     * @throws AfterActionReportAlreadyExistsException
     */
    public function createOrUpdate(AfterActionReport $aar, string $attendanceJson, bool $isNew): void
    {
        if ($isNew) {
            $this->ensureNotDuplicate($aar);
        }

        try {
            $attendance = json_decode($attendanceJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $attendance = [];
            foreach ($this->getAttendanceStates() as $state) {
                $attendance[$state] = [];
            }
        }
        $aar->setAttendance($attendance);

        if ($isNew) {
            /** @var Unit|null */
            $unit = $this->unitRepository->findOneBy(['perscomId' => $aar->getUnitId()]);
            $aar->setUnitName($unit?->getName() ?? 'unknown');
            $aar->setUnitPosition($unit?->getPosition() ?? 999);
        }
        $this->afterActionReportRepository->save($aar);

        if (!$isNew) {
            return;
        }

        if ($aar->getMission()->isCreateCombatRecords()) {
            $this->createCombatRecords($aar);
        }
        $this->handleAbsence($aar);
    }

    /**
     * @throws AfterActionReportAlreadyExistsException
     */
    private function ensureNotDuplicate(AfterActionReport $aar): void
    {
        $existing = $this->afterActionReportRepository->findBy([
            'mission' => $aar->getMission(),
            'unitId' => $aar->getUnitId(),
        ]);

        if (!empty($existing)) {
            throw new AfterActionReportAlreadyExistsException();
        }
    }

    public function getAttendanceStates(): array
    {
        $attendanceStates = $this->settingRepository->get('perscom.operations.attendance_states') ?? [];
        if (!is_array($attendanceStates)) {
            $attendanceStates = explode(',', $attendanceStates);
        }
        $attendanceStates = array_map('trim', array_filter($attendanceStates));

        return empty($attendanceStates)
            ? ['present', 'excused', 'absent']
            : $attendanceStates;
    }

    public function createCombatRecords(AfterActionReport $aar): void
    {
        $perscomUserIds = $aar->getAttendance()['present'] ?? [];
        if (empty($perscomUserIds)) {
            return;
        }

        $this->recordService->createRecord('combat', [
            'sendNotification' => true,
            'text' => $aar->getMission()->getCombatRecordText() ?: $this->getDefaultCombatRecordText($aar->getMission()),
            'users' => $perscomUserIds,
        ]);
    }

    private function getDefaultCombatRecordText(Mission $mission): string
    {
        return "Operation {$mission->getOperation()->getTitle()}: Mission {$mission->getTitle()}";
    }

    /**
     * @return array<PerscomUser>
     */
    public function findUsersByUnit(int $unitId): array
    {
        /** @var Unit|null $unit */
        $unit = $this->unitRepository->findOneBy(['perscomId' => $unitId]);
        if ($unit === null) {
            return [];
        }

        $users = $unit->getUsers()->toArray();
        $this->userService->sortPerscomUsers($users);
        return $users;
    }

    private function handleAbsence(AfterActionReport $aar): void
    {
        $absences = $aar->getAttendance()['absent'] ?? [];
        if (empty($absences)) {
            return;
        }

        $absentUsers = $this->perscomUserRepository->findByPerscomIds($absences);
        if (empty($absentUsers)) {
            return;
        }

        $this->handleAbsenceNotification($absentUsers, $aar);
        $this->handleConsecutiveAbsences($absentUsers, $aar);
    }

    /**
     * @param array<PerscomUser> $absentUsers
     */
    private function handleAbsenceNotification(array $absentUsers, AfterActionReport $aar)
    {
        $s = $this->settingRepository->get(...);
        $notificationEnabled = (bool)$s('perscom.operations.absent_notification');
        if (!$notificationEnabled) {
            return;
        }

        $notificationMessage = $s('perscom.operations.absent_notification_message');
        $notificationMessage = empty($notificationMessage)
            ? "You have been marked absent from mission {$aar->getMission()->getTitle()}"
            : $notificationMessage;

        foreach ($absentUsers as $user) {
            $this->notificationService->sendNotification(new Notification(
                GenericNotificationType::TYPE,
                $user->getUser(),
                [
                    'description' => $notificationMessage,
                    'image' => $this->packages->getUrl('bundles/forumifyperscomplugin/images/perscom.png'),
                    'title' => 'Mission absence',
                    'url' => $this->urlGenerator->generate('perscom_aar_view', ['id' => $aar->getId()]),
                ],
            ));
        }
    }

    /**
     * @param array<PerscomUser> $absentUsers
     */
    private function handleConsecutiveAbsences(array $absentUsers, AfterActionReport $aar): void
    {
        $s = $this->settingRepository->get(...);
        $consecutiveEnabled = (bool)$s('perscom.operations.consecutive_absent_notification');
        if (!$consecutiveEnabled) {
            return;
        }

        $consecutiveCount = (int)$s('perscom.operations.consecutive_absent_notification_count');
        if ($consecutiveCount < 1) {
            return;
        }

        /** @var array<AfterActionReport> $pastAars */
        $pastAars = $this->afterActionReportRepository
            ->createQueryBuilder('aar')
            ->join('aar.mission', 'm')
            ->where('aar.unitId = :unitId')
            ->orderBy('m.start', 'DESC')
            ->setMaxResults($consecutiveCount)
            ->setParameter('unitId', $aar->getUnitId())
            ->getQuery()
            ->getResult()
        ;

        if (count($pastAars) < $consecutiveCount) {
            // not enough AARs to check
            return;
        }

        $description = "You have been marked absent $consecutiveCount times in a row. Please contact your leadership immediately or risk punitive action!";
        $consecutiveMessage = $s('perscom.operations.consecutive_absent_notification_message');
        $consecutiveMessage = empty(strip_tags($consecutiveMessage)) ? $description : $consecutiveMessage;

        foreach ($absentUsers as $user) {
            if (!$this->isAbsentInAllAars($user, $pastAars)) {
                continue;
            }

            $this->notificationService->sendNotification(new Notification(
                GenericEmailNotificationType::TYPE,
                $user->getUser(),
                [
                    'description' => $description,
                    'emailActionLabel' => 'View After Action Report',
                    'emailContent' => $consecutiveMessage,
                    'emailTemplate' => '@ForumifyPerscomPlugin/emails/notifications/consecutive_absence.html.twig',
                    'image' => $this->packages->getUrl('bundles/forumifyperscomplugin/images/perscom.png'),
                    'title' => "You have been marked absent $consecutiveCount times consecutively!",
                    'url' => $this->urlGenerator->generate('perscom_aar_view', ['id' => $aar->getId()]),
                ]
            ));
        }

        $consecutiveStatusId = (int)$s('perscom.operations.consecutive_absent_status');
        if ($consecutiveStatusId < 1) {
            return;
        }

        /** @var Status|null $consecutiveStatus */
        $consecutiveStatus = $this->statusRepository->find($consecutiveStatusId);
        if ($consecutiveStatus === null) {
            return;
        }

        $absentUserIds = array_map(fn (PerscomUser $user) => $user->getId(), $absentUsers);
        $this->recordService->createRecord('assignment', [
            'sendNotification' => true,
            'status' => $consecutiveStatus,
            'text' => "Status updated to {$consecutiveStatus->getName()} due to consecutive absences.",
            'type' => 'primary',
            'users' => $absentUserIds,
        ]);
    }

    private function isAbsentInAllAars(PerscomUser $user, array $pastAars): bool
    {
        foreach ($pastAars as $pastAar) {
            $absences = $pastAar->getAttendance()['absent'] ?? [];
            if (!in_array($user->getId(), $absences)) {
                return false;
            }
        }
        return true;
    }
}
