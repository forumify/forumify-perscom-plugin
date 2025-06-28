<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\EventSubscriber;

use Forumify\Admin\Crud\Event\PreSaveCrudEvent;
use Forumify\Core\Service\MediaService;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class QualificationCrudSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly FilesystemOperator $perscomAssetStorage,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [PreSaveCrudEvent::getName(Qualification::class) => 'preSaveQualification'];
    }

    /**
     * @param PreSaveCrudEvent<Qualification> $event
     */
    public function preSaveQualification(PreSaveCrudEvent $event): void
    {
        $qualification = $event->getEntity();
        $form = $event->getForm();
        $newImage = $form->get('newImage')->getData();
        if ($newImage instanceof UploadedFile) {
            $image = $this->mediaService->saveToFilesystem($this->perscomAssetStorage, $newImage);
            $qualification->setImage($image);
            $qualification->setImageDirty();
        }
    }
}
