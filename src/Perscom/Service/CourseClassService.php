<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Doctrine\Common\Collections\Collection;
use Forumify\Calendar\Entity\CalendarEvent;
use Forumify\Calendar\Repository\CalendarEventRepository;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassStudent;
use Forumify\PerscomPlugin\Perscom\Exception\PerscomException;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CourseClassService
{
    public function __construct(
        private readonly RecordService $recordService,
        private readonly CourseClassRepository $courseClassRepository,
        private readonly CalendarEventRepository $calendarEventRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function createCalendarEvent(CourseClass $class): void
    {
        $calendar = $class->getCalendar();
        if ($calendar === null) {
            return;
        }

        $event = $this->createOrUpdateCalendarEvent($class);
        $class->setEvent($event);
        $this->courseClassRepository->save($class);
    }

    public function createOrUpdateCalendarEvent(CourseClass $class): CalendarEvent
    {
        $event = $class->getEvent() ?? new CalendarEvent();
        $event->setCalendar($class->getCalendar());
        $event->setTitle($class->getTitle());
        $event->setStart($class->getStart());

        $classLink = $this->urlGenerator->generate('perscom_course_class_view', ['id' => $class->getId()]);
        $content = "<p><a href='$classLink' target='_blank'><i class='ph ph-arrow-square-out'></i> View class</a></p>";
        $event->setContent($content);

        $this->calendarEventRepository->save($event);
        return $event;
    }

    /**
     * @throws PerscomException
     */
    public function processResult(CourseClass $class): void
    {
        $serviceRecords = $this->getServiceRecords($class);
        $this->recordService->createRecords('service', $serviceRecords, true);

        $qualificationRecords = $this->getQualificationRecords($class->getStudents());
        $this->recordService->createRecords('qualification', $qualificationRecords, true);
    }

    private function getServiceRecords(CourseClass $class): array
    {
        $records = [];

        foreach ($class->getInstructors() as $instructor) {
            $text = 'Attended ' . $class->getTitle();
            if ($instructor->getInstructor() !== null) {
                $text .= ' as ' . $instructor->getInstructor()->getTitle();
            }

            $records[] = [
                'user_id' => $instructor->getPerscomUserId(),
                'text' =>  $text,
            ];
        }

        $students = $class->getStudents()->filter(fn (CourseClassStudent $s) => $s->getResult() === 'passed');
        /** @var CourseClassStudent $student */
        foreach ($students as $student) {
            $records[] = [
                'user_id' => $student->getPerscomUserId(),
                'text' => $student->getServiceRecordTextOverride() ?: "Graduated {$class->getTitle()}",
            ];
        }

        return $records;
    }

    /**
     * @param Collection<int, CourseClassStudent> $students
     */
    private function getQualificationRecords(Collection $students): array
    {
        $records = [];
        foreach ($students as $student) {
            foreach ($student->getQualifications() as $qualificationId) {
                $records[] = [
                    'user_id' => $student->getPerscomUserId(),
                    'qualification_id' => $qualificationId,
                ];
            }
        }

        return $records;
    }
}
