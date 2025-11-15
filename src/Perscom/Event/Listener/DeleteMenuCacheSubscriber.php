<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event\Listener;

use Forumify\Admin\Crud\Event\PostSaveCrudEvent;
use Forumify\Core\Twig\Extension\MenuRuntime;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Event\RecordsCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\CacheInterface;

class DeleteMenuCacheSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RecordsCreatedEvent::class => 'onRecordsCreated',
            PostSaveCrudEvent::getName(PerscomUser::class) => 'postSaveUser',
        ];
    }

    public function onRecordsCreated(RecordsCreatedEvent $event): void
    {
        foreach ($event->records as $record) {
            if (!$record instanceof AssignmentRecord) {
                continue;
            }

            $forumUser = $record->getUser()->getUser();
            if ($forumUser !== null) {
                $this->cache->delete(MenuRuntime::createMenuCacheKey($forumUser));
            }
        }
    }

    /**
     * @param PostSaveCrudEvent<PerscomUser> $event
     */
    public function postSaveUser(PostSaveCrudEvent $event): void
    {
        $forumifyUser = $event->getEntity()->getUser();
        if ($forumifyUser === null) {
            return;
        }
        $this->cache->delete(MenuRuntime::createMenuCacheKey($forumifyUser));
    }
}
