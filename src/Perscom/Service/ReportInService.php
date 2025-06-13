<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use DateTime;
use Exception;
use Forumify\Core\Entity\Notification;
use Forumify\Core\Notification\GenericEmailNotificationType;
use Forumify\Core\Notification\NotificationService;
use Forumify\Core\Repository\SettingRepository;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\ReportIn;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\ReportInRepository;
use Forumify\Plugin\Service\PluginVersionChecker;
use Perscom\Data\FilterObject;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ReportInService
{
    private ?array $failureStatus = null;

    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly SettingRepository $settingRepository,
        private readonly PluginVersionChecker $pluginVersionChecker,
        private readonly ReportInRepository $reportInRepository,
        private readonly UserRepository $userRepository,
        private readonly NotificationService $notificationService,
        private readonly RecordService $recordService,
        private readonly PerscomUserService $perscomUserService,
        private readonly Packages $packages,
        private readonly UrlGeneratorInterface $urlGenerator,
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
            $perscomUserId = $perscomUser['id'];

            $lastReportIn = $this->reportInRepository->find($perscomUserId);
            if ($lastReportIn === null) {
                $reportIn = new ReportIn();
                $reportIn->setPerscomUserId($perscomUserId);
                $reportIn->setLastReportInDate($now);
                $this->reportInRepository->save($reportIn, false);
                continue;
            }

            $diff = (int)$now->diff($lastReportIn->getLastReportInDate())->format("%a");
            if ($diff > $period) {
                $failures[] = $perscomUser;

                $lastReportIn->setPreviousStatusId($perscomUser['status_id']);
                $this->reportInRepository->save($lastReportIn, false);
                continue;
            }

            if ($warningsEnabled && $diff > $warningPeriod) {
                $this->sendWarning($perscomUser['email'], $period - $diff + 1);
            }
        }
        $this->reportInRepository->flush();

        if (empty($failures)) {
            return;
        }

        $failureStatus = $this->getFailureStatus();
        $this->recordService->createRecord('assignment', [
            'users' => array_column($failures, 'id'),
            'type' => 'primary',
            'status_id' => $failureStatus['id'],
            'text' => "Status updated to {$failureStatus['name']} due to a failure to report in.",
            'sendNotification' => true,
        ]);

        foreach ($failures as $perscomUser) {
            $this->sendFailure($perscomUser, $period);
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

        $perscomUser = $this->perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            return false;
        }

        $enabledStatusIds = $this->settingRepository->get('perscom.report_in.enabled_status');
        $enabledStatusIds[] = (int)$this->settingRepository->get('perscom.report_in.failure_status');

        return in_array($perscomUser['status_id'], $enabledStatusIds, true);
    }

    public function reportIn(): bool
    {
        $perscomUser = $this->perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            return false;
        }

        $perscomUserId = $perscomUser['id'];
        $lastReportIn = $this->reportInRepository->find($perscomUserId);
        if ($lastReportIn === null) {
            $lastReportIn = new ReportIn();
            $lastReportIn->setPerscomUserId($perscomUserId);
        }
        $lastReportIn->setLastReportInDate(new DateTime());

        $updated = false;
        if ($previousStatus = $lastReportIn->getPreviousStatusId()) {
            $this->recordService->createRecord('assignment', [
                'users' => [$perscomUserId],
                'type' => 'primary',
                'status_id' => $previousStatus,
                'text' => "Status reverted back to original due to reporting in.",
            ]);
            $lastReportIn->setPreviousStatusId(null);
            $updated = true;
        }

        $this->reportInRepository->save($lastReportIn);

        return $updated;
    }

    private function getFailureStatus(): array
    {
        if ($this->failureStatus !== null) {
            return $this->failureStatus;
        }

        $failureStatusId = (int)$this->settingRepository->get('perscom.report_in.failure_status');
        try {
            $this->failureStatus = $this->perscomFactory
                ->getPerscom()
                ->statuses()
                ->get($failureStatusId)
                ->json('data');
            return $this->failureStatus;
        } catch (Exception) {
        }
        throw new \RuntimeException('No failure status found.');
    }

    private function getUsersToCheck(): array
    {
        $enabledStatuses = $this->settingRepository->get('perscom.report_in.enabled_status');
        if (empty($enabledStatuses) || !is_array($enabledStatuses)) {
            return [];
        }

        try {
            return $this->perscomFactory
                ->getPerscom()
                ->users()
                ->search(filter: new FilterObject('status_id', 'in', $enabledStatuses), limit: 9999)
                ->json('data');
        } catch (Exception) {
            return [];
        }
    }

    private function sendWarning(string $email, int $daysLeft): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user === null) {
            return;
        }

        $status = $this->getFailureStatus()['name'] ?? '';
        $this->notificationService->sendNotification(new Notification(
            GenericEmailNotificationType::TYPE,
            $user,
            [
                'title' => 'perscom.notification.report_in_warning.title',
                'titleParams' => ['status' => $status],
                'description' => 'perscom.notification.report_in_warning.description',
                'descriptionParams' => [
                    'status' => $status,
                    'days' => $daysLeft,
                ],
                'image' => $this->packages->getUrl('bundles/forumifyperscomplugin/images/perscom.png'),
                'url' => $this->urlGenerator->generate('perscom_operations_center'),
                'emailActionLabel' => 'perscom.notification.report_in_warning.action'
            ],
        ));
    }

    private function sendFailure(array $perscomUser, int $period): void
    {
        $user = $this->userRepository->findOneBy(['email' => $perscomUser['email']]);
        if ($user === null) {
            return;
        }

        $status = $this->getFailureStatus()['name'] ?? '';
        $this->notificationService->sendNotification(new Notification(
            GenericEmailNotificationType::TYPE,
            $user,
            [
                'title' => 'perscom.notification.report_in_failure.title',
                'titleParams' => ['status' => $status],
                'description' => 'perscom.notification.report_in_failure.description',
                'descriptionParams' => [
                    'status' => $status,
                    'days' => $period,
                ],
                'image' => $this->packages->getUrl('bundles/forumifyperscomplugin/images/perscom.png'),
                'url' => $this->urlGenerator->generate('perscom_operations_center'),
                'emailActionLabel' => 'perscom.notification.report_in_failure.action'
            ],
        ));
    }
}
