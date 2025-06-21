<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Doctrine\Common\Collections\Collection;
use Forumify\Calendar\Entity\CalendarEvent;
use Forumify\Calendar\Repository\CalendarEventRepository;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassStudent;
use Forumify\PerscomPlugin\Perscom\Entity\Record\QualificationRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RecordInterface;
use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;
use Forumify\PerscomPlugin\Perscom\Exception\PerscomException;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassRepository;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Forumify\PerscomPlugin\Perscom\Repository\QualificationRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CourseClassService
{
    public function __construct(
        private readonly RecordService $recordService,
        private readonly CourseClassRepository $courseClassRepository,
        private readonly CalendarEventRepository $calendarEventRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly QualificationRepository $qualificationRepository,
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
        $records = [];

        $this->addServiceRecords($records, $class);
        $this->addQualificationRecords($records, $class->getStudents());

        $this->recordService->createRecords($records, true);
    }

    /**
     * @param RecordInterface[] $records
     */
    private function addServiceRecords(array &$records, CourseClass $class): void
    {
        foreach ($class->getInstructors() as $instructor) {
            $recipient = $this->perscomUserRepository->findOneBy(['perscomId' => $instructor->getPerscomUserId()]);
            if ($recipient === null) {
                continue;
            }

            $text = 'Attended ' . $class->getTitle();
            if ($instructor->getInstructor() !== null) {
                $text .= ' as ' . $instructor->getInstructor()->getTitle();
            }

            $record = new ServiceRecord();
            $record->setUser($recipient);
            $record->setText($text);
            $records[] = $record;
        }

        $students = $class->getStudents()->filter(fn (CourseClassStudent $s) => $s->getResult() === 'passed');
        /** @var CourseClassStudent $student */
        foreach ($students as $student) {
            $recipient = $this->perscomUserRepository->findOneBy(['perscomId' => $student->getPerscomUserId()]);
            if ($recipient === null) {
                continue;
            }

            $text = $student->getServiceRecordTextOverride() ?: "Graduated {$class->getTitle()}";

            $record = new ServiceRecord();
            $record->setUser($recipient);
            $record->setText($text);
            $records[] = $record;
        }
    }

    /**
     * @param RecordInterface[] $records
     * @param Collection<int, CourseClassStudent> $students
     */
    private function addQualificationRecords(array &$records, Collection $students): void
    {
        $qualifications = [];

        foreach ($students as $student) {
            $recipient = $this->perscomUserRepository->findOneBy(['perscomId' => $student->getPerscomUserId()]);
            if ($recipient === null) {
                continue;
            }

            foreach ($student->getQualifications() as $qualificationId) {
                // TODO: make qualifications a FK instead of just an id.
                $qualifications[$qualificationId] = isset($qualifications[$qualificationId])
                    ? $qualifications[$qualificationId]
                    : $this->qualificationRepository->findOneBy(['perscomId' => $qualificationId]);

                if ($qualifications[$qualificationId] === null) {
                    continue;
                }

                $record = new QualificationRecord();
                $record->setQualification($qualifications[$qualificationId]);
                $record->setUser($recipient);
                $records[] = $record;
            }
        }
    }
}
