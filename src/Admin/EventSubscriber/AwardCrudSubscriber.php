<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\EventSubscriber;

use Forumify\Admin\Crud\Event\PreSaveCrudEvent;
use Forumify\Core\Service\MediaService;
use Forumify\PerscomPlugin\Perscom\Entity\Award;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AwardCrudSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly FilesystemOperator $perscomAssetStorage,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [PreSaveCrudEvent::getName(Award::class) => 'preSaveAward'];
    }

    /**
     * @param PreSaveCrudEvent<Award> $event
     */
    public function preSaveAward(PreSaveCrudEvent $event): void
    {
        $award = $event->getEntity();
        $form = $event->getForm();
        $newImage = $form->get('newAwardImage')->getData();
        if ($newImage instanceof UploadedFile) {
            $image = $this->mediaService->saveToFilesystem($this->perscomAssetStorage, $newImage);
            $award->setImage($image);
            $award->setImageId(null);
        }
    }
}
