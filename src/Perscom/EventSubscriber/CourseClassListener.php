<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Service\CourseClassService;

#[AsEntityListener(Events::postPersist, 'postSave', entity: CourseClass::class)]
#[AsEntityListener(Events::postUpdate, 'postSave', entity: CourseClass::class)]
#[AsEntityListener(Events::postRemove, 'postRemove', entity: CourseClass::class)]
class CourseClassListener
{
    public function __construct(
        private readonly CourseClassService $courseClassService,
    ) {
    }

    public function postSave(CourseClass $class): void
    {
        $this->courseClassService->createOrUpdateCalendarEvent($class);
    }

    public function postRemove(CourseClass $class): void
    {
        $this->courseClassService->removeCalendarEvent($class);
    }
}
