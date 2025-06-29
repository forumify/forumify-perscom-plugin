<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\EventSubscriber;

use Exception;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomEntityInterface;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Sync\EventSubscriber\Event\PostSyncToPerscomEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Path;

class SyncUserImagesSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(param: 'kernel.project_dir')]
        private readonly string $rootDir,
        private readonly PerscomFactory $perscomFactory,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostSyncToPerscomEvent::class => 'handlePostSync'
        ];
    }

    public function handlePostSync(PostSyncToPerscomEvent $event): void
    {
        $cs = $event->changeSet;
        foreach ($cs['create'] as $createdEntity) {
            $this->handleImageSync($createdEntity);
        }

        foreach ($cs['update'] as $updatedEntity) {
            $this->handleImageSync($updatedEntity);
        }
    }

    private function handleImageSync(PerscomEntityInterface $entity): void
    {
        if (!$entity instanceof PerscomUser) {
            return;
        }

        $this->uploadSignature($entity);
        $this->uploadUniform($entity);
    }

    private function uploadSignature(PerscomUser $user): void
    {
        if (!$user->isSignatureDirty()) {
            return;
        }

        $perscom = $this->perscomFactory->getPerscom(true);

        $userId = $user->getPerscomId();
        if ($user->getPerscomSignature()) {
            try {
                $perscom->users()->profile_photo($userId)->delete();
            } catch (Exception) {
            }
        }

        if (empty($user->getSignature())) {
            return;
        }

        $fullPath = Path::join($this->rootDir, 'public', 'storage', 'perscom', $user->getSignature());
        try {
            $result = $perscom->users()->profile_photo($userId)->create($fullPath)->array('data');
        } catch (Exception) {
        }

        $user->setPerscomSignature($result['profile_photo']);
        $user->setSignatureDirty(false);
    }

    private function uploadUniform(PerscomUser $user): void
    {
        if (!$user->isUniformDirty()) {
            return;
        }

        $perscom = $this->perscomFactory->getPerscom(true);

        $userId = $user->getPerscomId();
        if ($user->getPerscomUniform()) {
            try {
                $perscom->users()->cover_photo($userId)->delete();
            } catch (Exception) {
            }
        }

        if (empty($user->getUniform())) {
            return;
        }

        $fullPath = Path::join($this->rootDir, 'public', 'storage', 'perscom', $user->getUniform());
        try {
            $result = $perscom->users()->cover_photo($userId)->create($fullPath)->array('data');
        } catch (Exception) {
        }

        $user->setPerscomSignature($result['cover_photo']);
        $user->setUniformDirty(false);
    }
}
