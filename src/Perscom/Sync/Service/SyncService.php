<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Entity;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomEntityInterface;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomSyncResult;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Sync\EventSubscriber\Event\PostSyncToPerscomEvent;
use Forumify\PerscomPlugin\Perscom\Sync\Exception\SyncLockedException;
use Perscom\Contracts\Batchable;
use Perscom\Contracts\ResourceContract;
use Perscom\Data\ResourceObject;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use function Symfony\Component\String\u;

class SyncService
{
    public const SYNC_LOCK_NAME = 'perscom.sync.mutex';
    public const SETTING_SYNC_ENABLED = 'perscom.sync.enabled';
    public const SETTING_IS_INITIAL_SYNC_COMPLETED = 'perscom.sync.is_initial_completed';

    private bool $isRunning = false;
    private readonly Perscom $perscom;
    private PerscomSyncResult $result;

    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly EntityManagerInterface $em,
        private readonly NormalizerInterface&DenormalizerInterface $normalizer,
        private readonly LockFactory $lockFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
        PerscomFactory $perscomFactory,
    ) {
        $this->perscom = $perscomFactory->getPerscom(true);
    }

    public function syncAll(?int $resultId = null): void
    {
        if (!$this->isSyncEnabled()) {
            return;
        }

        $this->isRunning = true;
        $this->setResultForSync($resultId);

        $mutex = $this->lockFactory->createLock(self::SYNC_LOCK_NAME, 1800);
        $mutex->acquire(true);

        set_time_limit(0);
        ini_set('memory_limit', -1);

        $start = microtime(true);
        $this->result->logMessage('Sync started.');

        try {
            $this->syncEntities();
            $this->result->setSuccess(true);
        } catch (Exception $ex) {
            $this->result->setSuccess(false);
            $this->result->logMessage($ex->getMessage() . "\nTrace: " . $ex->getTraceAsString());
        }

        $this->result->setEnded();
        $this->result->logMessage('Sync finished.');
        $this->result->logMessage('Completed in: ' . number_format(microtime(true) - $start, 3) . ' seconds.');

        $this->em->persist($this->result);
        $this->em->flush();

        // Ensure the entity manager is cleared to avoid leaking memory in message handlers
        $this->em->clear();
        $mutex->release();
    }

    private function setResultForSync(?int $resultId): void
    {
        if ($resultId) {
            $result = $this->em->getRepository(PerscomSyncResult::class)->find($resultId);
            if ($result !== null) {
                $this->result = $result;
                return;
            }
        }

        $result = new PerscomSyncResult();
        $this->result = $result;
        $this->em->persist($result);
        $this->em->flush();

        $this->result = $result;
    }

    private function syncEntities(): void
    {
        $p = $this->perscom;
        $awards = $this->syncAllOfResource($p->awards(), Entity\Award::class, ['image']);
        $documents = $this->syncAllOfResource($p->documents(), Entity\Document::class);
        $positions = $this->syncAllOfResource($p->positions(), Entity\Position::class);
        $qualifications = $this->syncAllOfResource($p->qualifications(), Entity\Qualification::class, ['image']);
        $ranks = $this->syncAllOfResource($p->ranks(), Entity\Rank::class, ['image']);
        $specialties = $this->syncAllOfResource($p->specialties(), Entity\Specialty::class);
        $statuses = $this->syncAllOfResource($p->statuses(), Entity\Status::class);
        $units = $this->syncAllOfResource($p->units(), Entity\Unit::class);
        $this->syncAllOfResource($p->groups(), Entity\Roster::class, ['units'], context: ['units' => $units]);
        $users = $this->syncAllOfResource($p->users(), Entity\PerscomUser::class, context: [
            'positions' => $positions,
            'ranks' => $ranks,
            'specialties' => $specialties,
            'statuses' => $statuses,
            'units' => $units,
        ]);
        $this->syncAllOfResource(
            $p->assignmentRecords(),
            Entity\Record\AssignmentRecord::class,
            context: [
                'documents' => $documents,
                'positions' => $positions,
                'specialties' => $specialties,
                'statuses' => $statuses,
                'units' => $units,
                'users' => $users,
            ],
            batchSize: 1000,
        );
        $this->syncAllOfResource(
            $p->awardRecords(),
            Entity\Record\AwardRecord::class,
            context: [
                'awards' => $awards,
                'documents' => $documents,
                'users' => $users,
            ],
            batchSize: 1000,
        );
        $this->syncAllOfResource(
            $p->combatRecords(),
            Entity\Record\CombatRecord::class,
            context: [
                'documents' => $documents,
                'users' => $users,
            ],
            batchSize: 1000,
        );
        $this->syncAllOfResource(
            $p->qualificationRecords(),
            Entity\Record\QualificationRecord::class,
            context: [
                'documents' => $documents,
                'qualifications' => $qualifications,
                'users' => $users,
            ],
            batchSize: 1000,
        );
        $this->syncAllOfResource(
            $p->rankRecords(),
            Entity\Record\RankRecord::class,
            context: [
                'documents' => $documents,
                'ranks' => $ranks,
                'users' => $users,
            ],
            batchSize: 1000,
        );
        $this->syncAllOfResource(
            $p->serviceRecords(),
            Entity\Record\ServiceRecord::class,
            context: [
                'documents' => $documents,
                'users' => $users,
            ],
            batchSize: 1000,
        );

        $forms = $this->syncAllOfResource($p->forms(), Entity\Form::class, ['fields'], ['statuses' => $statuses]);
        $this->syncAllOfResource($p->submissions(), Entity\FormSubmission::class, ['statuses', 'statuses.record'], [
            'forms' => $forms,
            'statuses' => $statuses,
            'users' => $users,
        ]);

        $isInitialSyncDone = $this->settingRepository->get(SyncService::SETTING_IS_INITIAL_SYNC_COMPLETED) ?? false;
        if (!$isInitialSyncDone) {
            $this->settingRepository->set(SyncService::SETTING_IS_INITIAL_SYNC_COMPLETED, true);
        }
    }

    /**
     * @param array{
     *      create: array<PerscomEntityInterface>,
     *      update: array<PerscomEntityInterface>,
     *      delete: array<class-string<PerscomEntityInterface>, array<int>>,
     *  } $changeSet
     */
    public function syncToPerscom(array $changeSet): void
    {
        $mutex = $this->lockFactory->createLock(self::SYNC_LOCK_NAME);
        $locked = $mutex->acquire(false);
        if (!$locked) {
            throw new SyncLockedException();
        }

        $this->isRunning = true;

        $toCreate = $this->indexByClass($changeSet['create']);
        foreach ($toCreate as $class => $entities) {
            $resource = $class::getPerscomResource($this->perscom);
            if ($resource instanceof Batchable) {
                $this->batchCreate($resource, $entities);
            } else {
                $this->batchCreateSeq($resource, $entities);
            }
        }

        $toUpdate = $this->indexByClass($changeSet['update']);
        foreach ($toUpdate as $class => $entities) {
            $resource = $class::getPerscomResource($this->perscom);
            if ($resource instanceof Batchable) {
                $this->batchUpdate($resource, $entities);
            } else {
                $this->batchUpdateSeq($resource, $entities);
            }
        }

        foreach ($changeSet['delete'] as $class => $ids) {
            $resource = $class::getPerscomResource($this->perscom);
            if ($resource instanceof Batchable) {
                $resource->batchDelete(array_map(fn (int $id) => new ResourceObject($id), $ids));
            } else {
                foreach ($ids as $id) {
                    $resource->delete($id);
                }
            }
        }

        $this->eventDispatcher->dispatch(new PostSyncToPerscomEvent($changeSet));

        $this->em->flush();
        $mutex->release();
    }

    /**
     * @param array<PerscomEntityInterface> $entities
     */
    public function batchCreate(Batchable $resource, array $entities): void
    {
        $resources = array_map(
            fn (PerscomEntityInterface $entity) => new ResourceObject(
                null,
                $this->normalizer->normalize($entity, 'perscom_array'),
            ),
            $entities,
        );
        $result = $resource->batchCreate($resources)->array('data');

        foreach ($result as $key => $res) {
            $entity = $entities[$key];
            $entity->setPerscomId($res['id']);
            $entity->setDirty(false);
        }
    }

    /**
     * @param array<PerscomEntityInterface> $entities
     */
    public function batchCreateSeq(ResourceContract $resource, array $entities): void
    {
        foreach ($entities as $entity) {
            $result = $resource
                ->create($this->normalizer->normalize($entity, 'perscom_array'))
                ->array('data')
            ;
            $entity->setPerscomId($result['id']);
            $entity->setDirty(false);
        }
    }

    /**
     * @param array<PerscomEntityInterface> $entities
     */
    public function batchUpdate(Batchable $resource, array $entities): void
    {
        $resources = array_map(
            fn (PerscomEntityInterface $entity) => new ResourceObject(
                $entity->getPerscomId(),
                $this->normalizer->normalize($entity, 'perscom_array'),
            ),
            $entities,
        );
        $resource->batchUpdate($resources);

        foreach ($entities as $entity) {
            $entity->setDirty(false);
        }
    }

    /**
     * @param array<PerscomEntityInterface> $entities
     */
    public function batchUpdateSeq(ResourceContract $resource, array $entities): void
    {
        foreach ($entities as $entity) {
            $resource->update(
                $entity->getPerscomId(),
                $this->normalizer->normalize($entity, 'perscom_array'),
            );
            $entity->setDirty(false);
        }
    }

    /**
     * @template T of object
     * @param class-string<T> $entityClass
     * @return array<int, T> all entities indexed by Perscom ID
     */
    private function syncAllOfResource(
        ResourceContract $resource,
        string $entityClass,
        ?array $includes = [],
        ?array $context = [],
        ?int $batchSize = 100,
    ): array {
        // 100ms cooldown to avoid rate limits :)
        usleep(100000);
        $itemType = u($entityClass)->afterLast('\\')->toString();
        $this->result->logMessage("$itemType: Starting sync");

        $repository = $this->em->getRepository($entityClass);

        $page = 0;
        $lastPage = 0;
        $allItems = [];

        do {
            $page++;
            $res = $resource->all(include: $includes, page: $page, limit: $batchSize);
            $lastPage = $res->array('meta')['last_page'] ?? 0;
            $perscomItems = $res->array('data') ?? [];
            $perscomIds = array_column($perscomItems, 'id');

            $existingItems = $repository->findBy(['perscomId' => $perscomIds]);
            $existingItems = $this->indexByPerscomId($existingItems);

            foreach ($perscomItems as $item) {
                if (isset($allItems[$item['id']])) {
                    continue;
                }

                /** @var PerscomEntityInterface|null $existingItem */
                $existingItem = $existingItems[$item['id']] ?? null;
                if ($existingItem?->isDirty()) {
                    continue;
                }

                try {
                    /** @var PerscomEntityInterface $obj */
                    $obj = $this->normalizer->denormalize($item, $entityClass, 'perscom_array', [
                        AbstractNormalizer::OBJECT_TO_POPULATE => $existingItem,
                        ...$context,
                    ]);
                    $obj->setDirty(false);
                    $this->em->persist($obj);
                    $existingItems[$item['id']] = $obj;
                } catch (Exception $ex) {
                    $this->result->logMessage(
                        "$itemType: Skipping item with perscom id {$item['id']}: {$ex->getMessage()}"
                    );
                }
            }

            $allItems += $existingItems;
        } while ($page <= $lastPage);

        $this->em->flush();
        $this->em->createQueryBuilder()
            ->delete($entityClass, 'e')
            ->where('e.perscomId IS NOT NULL')
            ->andWhere('e.perscomId NOT IN (:allIds)')
            ->setParameter('allIds', array_keys($allItems))
            ->getQuery()
            ->execute()
        ;

        $this->result->logMessage("$itemType: Finished");
        return $allItems;
    }

    /**
     * @param array<PerscomEntityInterface> $items
     * @return array<int, PerscomEntityInterface>
     */
    private function indexByPerscomId(array $items): array
    {
        $arr = [];
        foreach ($items as $item) {
            if ($perscomId = $item->getPerscomId()) {
                $arr[$perscomId] = $item;
            }
        }
        return $arr;
    }

    /**
     * @template T of PerscomEntityInterface
     * @param array<T> $entities
     * @return array<class-string<T>, array<T>>
     */
    private function indexByClass(array $entities): array
    {
        $arr = [];
        foreach ($entities as $entity) {
            $arr[get_class($entity)][] = $entity;
        }
        return $arr;
    }

    /**
     * Used to disable doctrine listeners in the same process as the full sync.
     *
     * The setting can be used when connecting development environments to a production PERSCOM.io,
     * there is no UI for this setting to avoid users disabling sync and potentially running this
     * plugin as a standalone piece of software without a PERSCOM.io subscription.
     */
    public function isSyncEnabled(): bool
    {
        return !$this->isRunning && ($this->settingRepository->get(self::SETTING_SYNC_ENABLED) ?? true);
    }
}
