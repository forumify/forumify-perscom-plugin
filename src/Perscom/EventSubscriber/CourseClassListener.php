<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Forumify\Calendar\Repository\CalendarEventRepository;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Service\CourseClassService;

#[AsEntityListener(Events::postPersist, 'postPersist', entity: CourseClass::class)]
#[AsEntityListener(Events::postUpdate, 'postUpdate', entity: CourseClass::class)]
#[AsEntityListener(Events::postRemove, 'postRemove', entity: CourseClass::class)]
class CourseClassListener
{
    public function __construct(
        private readonly CourseClassService $courseClassService,
        private readonly CalendarEventRepository $calendarEventRepository,
    ) {
    }

    public function postPersist(CourseClass $class): void
    {
        $this->courseClassService->createCalendarEvent($class);
    }

    public function postUpdate(CourseClass $class): void
    {
        $event = $class->getEvent();
        if ($event !== null) {
            $this->courseClassService->createOrUpdateCalendarEvent($class);
        }
    }

    public function postRemove(CourseClass $class): void
    {
        $event = $class->getEvent();
        if ($event !== null) {
            $this->calendarEventRepository->remove($event);
        }
    }
}
