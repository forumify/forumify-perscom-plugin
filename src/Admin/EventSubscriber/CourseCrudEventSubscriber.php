<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\EventSubscriber;

use Forumify\Admin\Crud\Event\PreSaveCrudEvent;
use Forumify\Core\Service\MediaService;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CourseCrudEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly FilesystemOperator $assetStorage,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PreSaveCrudEvent::getName(Course::class) => 'preSaveCourse',
        ];
    }

    /**
     * @param PreSaveCrudEvent<Course> $event
     */
    public function preSaveCourse(PreSaveCrudEvent $event): void
    {
        $course = $event->getEntity();
        $form = $event->getForm();

        $newImage = $form->get('newImage')->getData();
        if (!($newImage instanceof UploadedFile)) {
            return;
        }

        $image = $this->mediaService->saveToFilesystem($this->assetStorage, $newImage);
        $course->setImage($image);
    }
}
