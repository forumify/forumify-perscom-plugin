<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Calendar\Entity\CalendarEvent;
use Forumify\Calendar\Repository\CalendarEventRepository;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassResult;
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
    public function processResult(CourseClassResult $result): void
    {
        $data = $result->getResult();

        $serviceRecords = $this->getServiceRecords($data);
        $this->recordService->createRecords('service', $serviceRecords, true);

        $qualificationRecords = $this->getQualificationRecords($data['students']);
        $this->recordService->createRecords('qualification', $qualificationRecords, true);
    }

    private function getServiceRecords(array $result): array
    {
        $records = [];

        $instructors = array_filter(array_combine(array_keys($result['instructors']), array_column($result['instructors'], 'service_record_text')));
        foreach ($instructors as $id => $text) {
            $records[] = ['user_id' => $id, 'text' => $text];
        }

        $students = array_filter(array_combine(array_keys($result['students']), array_column($result['students'], 'service_record_text')));
        foreach ($students as $id => $text) {
            $records[] = ['user_id' => $id, 'text' => $text];
        }

        return $records;
    }

    private function getQualificationRecords(array $students): array
    {
        $records = [];
        foreach ($students as $id => $result) {
            foreach ($result['qualifications'] as $qualificationId) {
                $records[] = [
                    'user_id' => $id,
                    'qualification_id' => $qualificationId,
                ];
            }
        }

        return $records;
    }
}
