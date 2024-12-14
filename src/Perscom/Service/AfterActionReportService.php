<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Exception;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\AfterActionReport;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\Exception\AfterActionReportAlreadyExistsException;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\AfterActionReportRepository;
use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class AfterActionReportService
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly SettingRepository $settingRepository,
        private readonly AfterActionReportRepository $afterActionReportRepository,
        private readonly RecordService $recordService,
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

        try {
            $unitId = $aar->getUnitId();
            $unit = $this->perscomFactory->getPerscom()->units()->get($unitId)->json('data');
        } catch (Exception) {
            $unit = null;
        }

        $aar->setAttendance($attendance);
        $aar->setUnitName($unit['name'] ?? 'unknown');
        $aar->setUnitPosition($unit['order'] ?? 100);
        $this->afterActionReportRepository->save($aar);

        if ($isNew && $aar->getMission()->isCreateCombatRecords()) {
            $this->createCombatRecords($aar);
        }
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
            'users' => $perscomUserIds,
            'text' => $aar->getMission()->getCombatRecordText() ?: $this->getDefaultCombatRecordText($aar->getMission()),
        ]);
    }

    private function getDefaultCombatRecordText(Mission $mission): string
    {
        return "Operation {$mission->getOperation()->getTitle()}: Mission {$mission->getTitle()}";
    }

    public function findUsersByUnit(int $unitId): array
    {
        try {
            $users = $this->perscomFactory
                ->getPerscom()
                ->units()
                ->get($unitId, [
                    'users',
                    'users.rank',
                    'users.rank.image',
                    'users.position',
                    'users.specialty',
                ])
                ->json('data')['users'] ?? [];
        } catch (Exception) {
            return [];
        }

        $this->sortUsers($users);
        return $users;
    }

    public function sortUsers(&$users): void
    {
        usort($users, static function (array $a, array $b): int {
            $aRank = $a['rank']['order'] ?? 100;
            $bRank = $b['rank']['order'] ?? 100;
            if ($aRank !== $bRank) {
                return $aRank - $bRank;
            }

            $aPos = $a['position']['order'] ?? 100;
            $bPos = $b['position']['order'] ?? 100;
            if ($aPos !== $bPos) {
                return $aPos - $bPos;
            }

            $aSpec = $a['specialty']['order'] ?? 100;
            $bSpec = $b['specialty']['order'] ?? 100;
            if ($aSpec !== $bSpec) {
                return $aSpec - $bSpec;
            }

            return strcmp($a['name'], $b['name']);
        });
    }
}
