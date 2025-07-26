<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\Service;

use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Entity\AfterActionReport;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassInstructor;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassStudent;
use Forumify\PerscomPlugin\Perscom\Entity\MissionRSVP;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomEntityInterface;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomSyncResult;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Forumify\PerscomPlugin\Perscom\Entity\ReportIn;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Forumify\PerscomPlugin\Perscom\Repository\AfterActionReportRepository;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassInstructorRepository;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassStudentRepository;
use Forumify\PerscomPlugin\Perscom\Repository\CourseRepository;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRSVPRepository;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomSyncResultRepository;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Forumify\PerscomPlugin\Perscom\Repository\QualificationRepository;
use Forumify\PerscomPlugin\Perscom\Repository\RankRepository;
use Forumify\PerscomPlugin\Perscom\Repository\ReportInRepository;
use Forumify\PerscomPlugin\Perscom\Repository\StatusRepository;
use Forumify\PerscomPlugin\Perscom\Repository\UnitRepository;

class MigrateOldDataService
{
    private PerscomSyncResult $result;

    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly PerscomSyncResultRepository $syncResultRepository,
        private readonly PerscomUserRepository $userRepository,
        private readonly StatusRepository $statusRepository,
        private readonly UnitRepository $unitRepository,
        private readonly MissionRSVPRepository $missionRSVPRepository,
        private readonly AfterActionReportRepository $afterActionReportRepository,
        private readonly ReportInRepository $reportInRepository,
        private readonly RankRepository $rankRepository,
        private readonly QualificationRepository $qualificationRepository,
        private readonly CourseRepository $courseRepository,
        private readonly CourseClassInstructorRepository $courseInstructorRepository,
        private readonly CourseClassStudentRepository $courseStudentRepository,
    ) {
    }

    public function migrate(int $resultId): void
    {
        $this->result = $this->syncResultRepository->find($resultId);
        $this->settingRepository->set(SyncService::SETTING_SYNC_ENABLED, false);

        $users = $this->indexByPerscomId($this->userRepository->findAll());
        $units = $this->indexByPerscomId($this->unitRepository->findAll());
        $statuses = $this->indexByPerscomId($this->statusRepository->findAll());
        $ranks = $this->indexByPerscomId($this->rankRepository->findAll());
        $qualifications = $this->indexByPerscomId($this->qualificationRepository->findAll());

        $this->migrateRsvps($users);
        $this->migrateAars($users, $units);
        $this->migrateReportIn($users, $statuses);
        $this->migrateCourses($ranks, $qualifications);
        $this->migrateInstructors($users);
        $this->migrateStudents($users, $qualifications);
        $this->migrateSettings($statuses);

        $this->settingRepository->set(SyncService::SETTING_SYNC_ENABLED, true);

        $this->result->logMessage('All data migrated successfully.');
        $this->syncResultRepository->save($this->result);
    }

    /**
     * @param array<PerscomUser> $users
     */
    private function migrateRsvps(array $users): void
    {
        $this->result->logMessage('Migrating RSVPs');

        /** @var MissionRSVP $rsvp */
        foreach ($this->missionRSVPRepository->findAll() as $rsvp) {
            $user = $users[$rsvp->getPerscomUserId()] ?? null;
            if ($user === null) {
                $this->missionRSVPRepository->remove($rsvp, false);
                continue;
            }

            $rsvp->setUser($user);
        }
        $this->missionRSVPRepository->flush();

        $this->result->logMessage('RSVPs migrated successfully.');
    }

    /**
     * @param array<PerscomUser> $users
     * @param array<Unit> $units
     */
    private function migrateAars(array $users, array $units): void
    {
        $this->result->logMessage('Migrating AARs');

        /** @var AfterActionReport $aar */
        foreach ($this->afterActionReportRepository->findAll() as $aar) {
            $unit = $units[$aar->getUnitId()] ?? null;
            if ($unit === null) {
                $this->afterActionReportRepository->remove($aar, false);
                continue;
            }

            $aar->setUnit($unit);
            $newAttendace = [];
            foreach ($aar->getAttendance() as $state => $userIds) {
                foreach ($userIds as $userId) {
                    $user = $users[$userId] ?? null;
                    if ($user !== null) {
                        $newAttendace[$state][] = $user->getId();
                    }
                }
            }
            $aar->setAttendance($newAttendace);
        }
        $this->afterActionReportRepository->flush();

        $this->result->logMessage('AARs migrated successfully.');
    }

    /**
     * @param array<PerscomUser> $users
     * @param array<Status> $statuses
     */
    private function migrateReportIn(array $users, array $statuses): void
    {
        $this->result->logMessage('Migrating Report Ins');

        /** @var ReportIn $reportIn */
        foreach ($this->reportInRepository->findAll() as $reportIn) {
            $user = $users[$reportIn->getPerscomUserId()] ?? null;
            if ($user === null) {
                $this->reportInRepository->remove($reportIn, false);
                continue;
            }

            $reportIn->setUser($user);
            $previousStatusId = $reportIn->getPreviousStatusId();
            if ($previousStatusId !== null && isset($statuses[$previousStatusId])) {
                $reportIn->setReturnStatus($statuses[$previousStatusId]);
            }
        }
        $this->reportInRepository->flush();

        $this->result->logMessage('Report Ins migrated successfully.');
    }

    /**
     * @param array<Rank> $ranks
     * @param array<Qualification> $qualifications
     */
    private function migrateCourses(array $ranks, array $qualifications): void
    {
        $this->result->logMessage('Migrating Courses');

        /** @var Course $course */
        foreach ($this->courseRepository->findAll() as $course) {
            if ($rankReq = $course->getRankRequirement()) {
                $rank = $ranks[$rankReq] ?? null;
                $course->setMinimumRank($rank);
            }

            $newPrerequisites = [];
            foreach ($course->getPrerequisites() as $qualId) {
                $qual = $qualifications[$qualId] ?? null;
                if ($qual !== null) {
                    $newPrerequisites[] = $qual->getId();
                }
            }
            $course->setPrerequisites($newPrerequisites);

            $newQualifications = [];
            foreach ($course->getQualifications() as $qualId) {
                $qual = $qualifications[$qualId] ?? null;
                if ($qual !== null) {
                    $newQualifications[] = $qual->getId();
                }
            }
            $course->setQualifications($newQualifications);
        }
        $this->courseRepository->flush();

        $this->result->logMessage('Courses migrated successfully.');
    }

    /**
     * @param array<PerscomUser> $users
     */
    private function migrateInstructors(array $users): void
    {
        $this->result->logMessage('Migrating Course Instructors');

        /** @var CourseClassInstructor $instructor */
        foreach ($this->courseInstructorRepository->findAll() as $instructor) {
            $user = $users[$instructor->getPerscomUserId()] ?? null;
            if ($user === null) {
                $this->courseInstructorRepository->remove($instructor, false);
                continue;
            }

            $instructor->setUser($user);
        }
        $this->courseInstructorRepository->flush();

        $this->result->logMessage('Course Instructors migrated successfully.');
    }

    /**
     * @param array<PerscomUser> $users
     * @param array<Qualification> $qualifications
     */
    private function migrateStudents(array $users, array $qualifications): void
    {
        $this->result->logMessage('Migrating Course Students');

        /** @var CourseClassStudent $student */
        foreach ($this->courseStudentRepository->findAll() as $student) {
            $user = $users[$student->getPerscomUserId()] ?? null;
            if ($user === null) {
                $this->courseStudentRepository->remove($student, false);
                continue;
            }

            $student->setUser($user);

            $newQualifications = [];
            foreach ($student->getQualifications() as $qualId) {
                $qual = $qualifications[$qualId] ?? null;
                if ($qual !== null) {
                    $newQualifications[] = $qual->getId();
                }
            }
            $student->setQualifications($newQualifications);
        }
        $this->courseStudentRepository->flush();

        $this->result->logMessage('Course Students migrated successfully.');
    }

    /**
     * @param array<Status> $statuses
     */
    private function migrateSettings(array $statuses): void
    {
        $this->result->logMessage('Migrating settings.');

        $settings = [];

        $enlistmentStatuses = $this->settingRepository->get('perscom.enlistment.status') ?? [];
        $newEnlistmentStatuses = [];
        foreach ($enlistmentStatuses as $statusId) {
            $status = $statuses[$statusId] ?? null;
            if ($status) {
                $newEnlistmentStatuses[] = $status->getId();
            }
        }
        $settings['perscom.enlistment.status'] = $newEnlistmentStatuses;

        $absentStatus = $this->settingRepository->get('perscom.operations.consecutive_absent_status');
        if ($absentStatus) {
            $status = $statuses[$absentStatus] ?? null;
            if ($status) {
                $settings['perscom.operations.consecutive_absent_status'] = $status->getId();
            }
        }

        $reportInStatuses = $this->settingRepository->get('perscom.report_in.enabled_status') ?? [];
        $newReportInStatuses = [];
        foreach ($reportInStatuses as $statusId) {
            $status = $statuses[$statusId] ?? null;
            if ($status) {
                $newReportInStatuses[] = $status->getId();
            }
        }
        $settings['perscom.report_in.enabled_status'] = $newReportInStatuses;

        $failureStatus = $this->settingRepository->get('perscom.report_in.failure_status');
        if ($failureStatus) {
            $status = $statuses[$failureStatus] ?? null;
            if ($status) {
                $settings['perscom.report_in.failure_status'] = $status->getId();
            }
        }

        $this->settingRepository->setBulk($settings);
        $this->result->logMessage('Settings migrated successfully.');
    }

    /**
     * @param array<PerscomEntityInterface> $items
     */
    private function indexByPerscomId(array $items): array
    {
        $indexed = [];
        foreach ($items as $item) {
            if ($item->getPerscomId() === null) {
                continue;
            }
            $indexed[$item->getPerscomId()] = $item;
        }
        return $indexed;
    }
}
