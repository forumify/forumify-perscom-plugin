<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event\Listener;

use Forumify\Core\Entity\User;
use Forumify\Core\Twig\Extension\MenuRuntime;
use Forumify\PerscomPlugin\Perscom\Event\RecordsCreatedEvent;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\CacheInterface;

class DeleteMenuCacheSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly CacheInterface $cache,
    ) {
    }


    public static function getSubscribedEvents(): array
    {
        return [
            RecordsCreatedEvent::class => 'onRecordsCreated'
        ];
    }


    public function onRecordsCreated(RecordsCreatedEvent $event): void
    {
        if ($event->type !== 'assignment') {
            return;
        }

        $userIds = array_column($event->records, 'user_id');
        $users = $this->perscomUserRepository->findByPerscomIds($userIds);
        foreach ($users as $user) {
            $this->clearMenuCache($user->getUser());
        }
    }

    private function clearMenuCache(User $user): void
    {
        $this->cache->delete(MenuRuntime::createMenuCacheKey($user));
    }
}
