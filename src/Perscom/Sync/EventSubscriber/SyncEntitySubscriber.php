<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Forumify\Core\Notification\ContextSerializer;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomEntityInterface;
use Forumify\PerscomPlugin\Perscom\Sync\Message\SyncToPerscomMessage;
use Forumify\PerscomPlugin\Perscom\Sync\Service\SyncService;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsDoctrineListener(event: Events::onFlush)]
#[AsDoctrineListener(event: Events::postFlush)]
class SyncEntitySubscriber
{
    /** @var array<PerscomEntityInterface> */
    private array $created = [];
    /** @var array<PerscomEntityInterface> */
    private array $updated = [];
    /** @var array<class-string<PerscomEntityInterface>, int[]> */
    private array $deleted = [];

    public function __construct(
        private readonly SyncService $syncService,
        private readonly MessageBusInterface $messageBus,
        private readonly ContextSerializer $contextSerializer,
    ) {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        if (!$this->syncService->isSyncEnabled()) {
            // Ensure we don't trigger PERSCOM.io updates during daily syncs
            return;
        }

        /** @var EntityManagerInterface $em */
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof PerscomEntityInterface) {
                $this->created[] = $entity;
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof PerscomEntityInterface) {
                $this->updated[] = $entity;
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof PerscomEntityInterface && $entity->getPerscomId() !== null) {
                $class = ClassUtils::getRealClass(get_class($entity));
                $this->deleted[$class][] = $entity->getPerscomId();
            }
        }
    }

    public function postFlush(): void
    {
        if (empty($this->created) && empty($this->updated) && empty($this->deleted)) {
            return;
        }

        $data = $this->contextSerializer->serialize([
            'create' => $this->created,
            'update' => $this->updated,
            'delete' => $this->deleted,
        ]);

        $this->messageBus->dispatch(new SyncToPerscomMessage($data));
    }
}
