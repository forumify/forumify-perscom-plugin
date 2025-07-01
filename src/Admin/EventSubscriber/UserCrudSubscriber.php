<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\EventSubscriber;

use Forumify\Admin\Crud\Event\PreSaveCrudEvent;
use Forumify\Core\Service\MediaService;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserCrudSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly FilesystemOperator $perscomAssetStorage,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [PreSaveCrudEvent::getName(PerscomUser::class) => 'preSaveUser'];
    }

    /**
     * @param PreSaveCrudEvent<PerscomUser> $event
     */
    public function preSaveUser(PreSaveCrudEvent $event): void
    {
        $form = $event->getForm();
        $user = $event->getEntity();

        $newUniform = $form->get('newUniform')->getData();
        if ($newUniform instanceof UploadedFile) {
            $uniform = $this->mediaService->saveToFilesystem($this->perscomAssetStorage, $newUniform);
            $user->setUniform($uniform);
            $user->setUniformDirty(true);
        }

        $newSignature = $form->get('newSignature')->getData();
        if (!($newSignature instanceof UploadedFile)) {
            return;
        }

        $signature = $this->mediaService->saveToFilesystem($this->perscomAssetStorage, $newSignature);
        $user->setSignature($signature);
        $user->setSignatureDirty(true);
    }
}
