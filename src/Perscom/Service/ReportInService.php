<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use DateTime;
use Forumify\Core\Entity\Notification;
use Forumify\Core\Entity\User;
use Forumify\Core\Notification\GenericEmailNotificationType;
use Forumify\Core\Notification\NotificationService;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\ReportIn;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Forumify\PerscomPlugin\Perscom\Repository\ReportInRepository;
use Forumify\PerscomPlugin\Perscom\Repository\StatusRepository;
use Forumify\Plugin\Service\PluginVersionChecker;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ReportInService
{
    private ?Status $failureStatus = null;

    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly PluginVersionChecker $pluginVersionChecker,
        private readonly ReportInRepository $reportInRepository,
        private readonly NotificationService $notificationService,
        private readonly RecordService $recordService,
        private readonly PerscomUserService $perscomUserService,
        private readonly Packages $packages,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly StatusRepository $statusRepository,
    ) {
    }

    public function runReportInChecks(): void
    {
        $usersToCheck = $this->getUsersToCheck();
        if (empty($usersToCheck)) {
            return;
        }

        $period = (int)$this->settingRepository->get('perscom.report_in.period');
        $warningPeriod = (int)$this->settingRepository->get('perscom.report_in.warning_period');
        $warningsEnabled = $warningPeriod > 0;

        $now = new DateTime();
        $failures = [];
        foreach ($usersToCheck as $perscomUser) {
            $lastReportIn = $this->reportInRepository->find($perscomUser->getPerscomId());
            if ($lastReportIn === null) {
                $reportIn = new ReportIn();
                $reportIn->setPerscomUserId($perscomUser->getPerscomId());
                $reportIn->setLastReportInDate($now);
                $this->reportInRepository->save($reportIn, false);
                continue;
            }

            $diff = (int)$now->diff($lastReportIn->getLastReportInDate())->format("%a");
            if ($diff > $period) {
                $failures[] = $perscomUser;

                $lastReportIn->setPreviousStatusId($perscomUser->getStatus()?->getPerscomId());
                $this->reportInRepository->save($lastReportIn, false);
                continue;
            }

            if (!$warningsEnabled || $diff <= $warningPeriod) {
                continue;
            }

            $user = $perscomUser->getUser();
            if ($user !== null) {
                $this->sendWarning($user, $period - $diff + 1);
            }
        }
        $this->reportInRepository->flush();

        if (empty($failures)) {
            return;
        }

        $failureStatus = $this->getFailureStatus();
        $this->recordService->createRecord('assignment', [
            'status' => $failureStatus,
            'text' => "Status updated to {$failureStatus->getName()} due to a failure to report in.",
            'type' => 'primary',
            'users' => $failures,
        ]);

        foreach ($failures as $perscomUser) {
            $user = $perscomUser->getUser();
            if ($user !== null) {
                $this->sendFailure($user, $period);
            }
        }
    }

    public function isEnabled(): bool
    {
        return $this->pluginVersionChecker->isVersionInstalled('forumify/forumify-perscom-plugin', 'premium')
            && $this->settingRepository->get('perscom.report_in.enabled')
            && (int)$this->settingRepository->get('perscom.report_in.period') > 0
            && $this->settingRepository->get('perscom.report_in.failure_status');
    }

    public function canReportIn(): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $statusId = $this->perscomUserService->getLoggedInPerscomUser()?->getStatus()?->getId();
        if ($statusId === null) {
            return false;
        }

        $enabledStatusIds = $this->settingRepository->get('perscom.report_in.enabled_status');
        $enabledStatusIds[] = (int)$this->settingRepository->get('perscom.report_in.failure_status');

        return in_array($statusId, $enabledStatusIds, true);
    }

    public function reportIn(): bool
    {
        $perscomUser = $this->perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            return false;
        }

        $perscomUserId = $perscomUser->getPerscomId();
        $lastReportIn = $this->reportInRepository->find($perscomUserId);
        if ($lastReportIn === null) {
            $lastReportIn = new ReportIn();
            $lastReportIn->setPerscomUserId($perscomUserId);
        }
        $lastReportIn->setLastReportInDate(new DateTime());

        $updated = false;
        if ($previousStatusId = $lastReportIn->getPreviousStatusId()) {
            $previousStatus = $this->statusRepository->findOneByPerscomId($previousStatusId);
            if ($previousStatus !== null) {
                $this->recordService->createRecord('assignment', [
                    'status' => $previousStatus,
                    'text' => "Status reverted back to original due to reporting in.",
                    'type' => 'primary',
                    'users' => [$perscomUser],
                ]);
            }
            $lastReportIn->setPreviousStatusId(null);
            $updated = true;
        }

        $this->reportInRepository->save($lastReportIn);

        return $updated;
    }

    private function getFailureStatus(): Status
    {
        if ($this->failureStatus !== null) {
            return $this->failureStatus;
        }

        $failureStatusId = (int)$this->settingRepository->get('perscom.report_in.failure_status');
        $this->failureStatus = $this->statusRepository->find($failureStatusId);
        if ($this->failureStatus === null) {
            throw new \RuntimeException('No failure status found.');
        }
        return $this->failureStatus;
    }

    /**
     * @return array<PerscomUser>
     */
    private function getUsersToCheck(): array
    {
        $enabledStatuses = $this->settingRepository->get('perscom.report_in.enabled_status');
        if (empty($enabledStatuses) || !is_array($enabledStatuses)) {
            return [];
        }

        return $this->perscomUserRepository->findBy(['status' => $enabledStatuses]);
    }

    private function sendWarning(User $user, int $daysLeft): void
    {
        $status = $this->getFailureStatus()->getName();
        $this->notificationService->sendNotification(new Notification(
            GenericEmailNotificationType::TYPE,
            $user,
            [
                'description' => 'perscom.notification.report_in_warning.description',
                'descriptionParams' => [
                    'days' => $daysLeft,
                    'status' => $status,
                ],
                'emailActionLabel' => 'perscom.notification.report_in_warning.action',
                'image' => $this->packages->getUrl('bundles/forumifyperscomplugin/images/perscom.png'),
                'title' => 'perscom.notification.report_in_warning.title',
                'titleParams' => ['status' => $status],
                'url' => $this->urlGenerator->generate('perscom_operations_center'),
            ],
        ));
    }

    private function sendFailure(User $user, int $period): void
    {
        $status = $this->getFailureStatus()->getName();
        $this->notificationService->sendNotification(new Notification(
            GenericEmailNotificationType::TYPE,
            $user,
            [
                'description' => 'perscom.notification.report_in_failure.description',
                'descriptionParams' => [
                    'days' => $period,
                    'status' => $status,
                ],
                'emailActionLabel' => 'perscom.notification.report_in_failure.action',
                'image' => $this->packages->getUrl('bundles/forumifyperscomplugin/images/perscom.png'),
                'title' => 'perscom.notification.report_in_failure.title',
                'titleParams' => ['status' => $status],
                'url' => $this->urlGenerator->generate('perscom_operations_center'),
            ],
        ));
    }
}
