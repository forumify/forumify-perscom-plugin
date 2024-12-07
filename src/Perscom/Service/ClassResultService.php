<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassResult;

class ClassResultService
{
    public function __construct(
        private readonly RecordService $recordService,
    ) {
    }

    public function processResult(CourseClassResult $result): void
    {
        $courseTitle = $result->getClass()->getCourse()->getTitle();
        $data = $result->getResult();

        $presentInstructors = array_filter($data['instructors']);
        if ($data['instructor_service_record'] && !empty($presentInstructors)) {
            $this->recordService->createRecord('service', [
                'sendNotification' => true,
                'users' => $presentInstructors,
                'text' => "Attended {$courseTitle} as instructor.",
            ]);
        }

        $students = $this->separateStudentsPerResult($data['students']);
        if ($data['student_service_record']) {
            if (!empty($students['passed'])) {
                $this->recordService->createRecord('service', [
                    'sendNotification' => true,
                    'users' => $students['passed'],
                    'text' => "Graduated from {$courseTitle} with a passing grade."
                ]);
            }

            if (!empty($students['failed'])) {
                $this->recordService->createRecord('service', [
                    'sendNotification' => true,
                    'users' => $students['failed'],
                    'text' => "Completed {$courseTitle} with a non-passing grade."
                ]);
            }

            if (!empty($students['no-show'])) {
                $this->recordService->createRecord('service', [
                    'sendNotification' => true,
                    'users' => $students['no-show'],
                    'text' => "Enrolled in {$courseTitle} but failed to show up."
                ]);
            }
        }

        $qualifications = $this->separateStudentsPerQualification($data['students']);
        foreach ($qualifications as $qualificationId => $studentIds) {
            $this->recordService->createRecord('qualification', [
                'sendNotification' => true,
                'users' => $studentIds,
                'qualification_id' => $qualificationId,
            ]);
        }
    }

    /**
     * @param array<int, array{result: string, qualifications: int[]}> $students
     * @return array<string, int>
     */
    private function separateStudentsPerResult(array $students): array
    {
        $return = [];
        foreach ($students as $studentId => $student) {
            $return[$student['result']][] = $studentId;
        }

        return $return;
    }

    /**
     * @param array<int, array{result: string, qualifications: int[]}> $students
     * @return array<int, int>
     */
    private function separateStudentsPerQualification(array $students): array
    {
        $return = [];
        foreach ($students as $studentId => $student) {
             foreach ($student['qualifications'] as $qualificationId) {
                 $return[$qualificationId][] = $studentId;
             }
        }
        return $return;
    }
}
