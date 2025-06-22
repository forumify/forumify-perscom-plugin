<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event\Listener;

use Forumify\Core\Twig\Extension\MenuRuntime;
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
            RecordsCreatedEvent::class => 'onRecordsCreated'
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
}
