<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\EventSubscriber;

use Forumify\Admin\Crud\Event\PreSaveCrudEvent;
use Forumify\Core\Service\MediaService;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RankCrudSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly FilesystemOperator $perscomAssetStorage,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [PreSaveCrudEvent::getName(Rank::class) => 'preSaveRank'];
    }

    public function preSaveRank(PreSaveCrudEvent $event): void
    {
        $rank = $event->getEntity();
        $form = $event->getForm();
        $newImage = $form->get('newImage')->getData();
        if ($newImage instanceof UploadedFile) {
            $image = $this->mediaService->saveToFilesystem($this->perscomAssetStorage, $newImage);
            $rank->setImage($image);
        }
    }
}
