<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Entity as Entity;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomEntityInterface;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Sync\Exception\SyncLockedException;
use Perscom\Contracts\Batchable;
use Perscom\Contracts\ResourceContract;
use Perscom\Data\ResourceObject;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Lock\LockFactory;

class SyncService
{
    public const SYNC_LOCK_NAME = 'perscom.sync.mutex';

    private bool $isRunning = false;
    private readonly Perscom $perscom;

    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly EntityManagerInterface $em,
        private readonly NormalizerInterface&DenormalizerInterface $normalizer,
        private readonly LockFactory $lockFactory,
        PerscomFactory $perscomFactory,
    ) {
        $this->perscom = $perscomFactory->getPerscom(true);
    }

    public function syncAll(): void
    {
        if (!$this->isSyncEnabled()) {
            return;
        }
        $this->isRunning = true;

        $mutex = $this->lockFactory->createLock(self::SYNC_LOCK_NAME, 1800);
        $mutex->acquire(true);

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
                'users' => $users,
                'documents' => $documents,
                'statuses' => $statuses,
                'units' => $units,
                'positions' => $positions,
                'specialties' => $specialties,
            ],
            batchSize: 1000,
        );
        $this->syncAllOfResource(
            $p->awardRecords(),
            Entity\Record\AwardRecord::class,
            context: [
                'users' => $users,
                'documents' => $documents,
                'awards' => $awards,
            ],
            batchSize: 1000,
        );
        $this->syncAllOfResource(
            $p->combatRecords(),
            Entity\Record\CombatRecord::class,
            context: [
                'users' => $users,
                'documents' => $documents,
            ],
            batchSize: 1000,
        );
        $this->syncAllOfResource(
            $p->qualificationRecords(),
            Entity\Record\QualificationRecord::class,
            context: [
                'users' => $users,
                'documents' => $documents,
                'qualifications' => $qualifications,
            ],
            batchSize: 1000,
        );
        $this->syncAllOfResource(
            $p->rankRecords(),
            Entity\Record\RankRecord::class,
            context: [
                'users' => $users,
                'documents' => $documents,
                'ranks' => $ranks,
            ],
            batchSize: 1000,
        );
        $this->syncAllOfResource(
            $p->serviceRecords(),
            Entity\Record\ServiceRecord::class,
            context: [
                'users' => $users,
                'documents' => $documents,
            ],
            batchSize: 1000,
        );

        // Ensure the entity manager is cleared to avoid leaking memory in message handlers
        $this->em->clear();
        $mutex->release();
    }

    /**
     * @var array{
     *      create: PerscomEntityInterface[],
     *      update: PerscomEntityInterface[],
     *      delete: array<class-string<PerscomEntityInterface>, int[]>,
     *  }
     */
    public function syncToPerscom(array $changeSet): void
    {
        $mutex = $this->lockFactory->createLock(self::SYNC_LOCK_NAME);
        $locked = $mutex->acquire(false);
        if (!$locked) {
            throw new SyncLockedException();
        }

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
                $resource->batchDelete($ids);
            } else {
                foreach ($ids as $id) {
                    $resource->delete($id);
                }
            }
        }

        $mutex->release();
    }

    /**
     * @var array<PerscomEntityInterface> $entities
     */
    public function batchCreate(Batchable $resource, array $entities): void
    {
        $resources = array_map(
            fn (PerscomEntityInterface $entity) => new ResourceObject(
                null,
                $this->normalizer->normalize($entity, 'perscom_array', ['action' => 'create']),
            ),
            $entities,
        );
        $resource->batchCreate($resources);
    }

    /**
     * @var array<PerscomEntityInterface> $entities
     */
    public function batchCreateSeq(ResourceContract $resource, array $entities): void
    {
        foreach ($entities as $entity) {
            $resource->create($this->normalizer->normalize($entity, 'perscom_array', ['action' => 'create']));
        }
    }

    /**
     * @var array<PerscomEntityInterface> $entities
     */
    public function batchUpdate(Batchable $resource, array $entities): void
    {
        $resources = array_map(
            fn (PerscomEntityInterface $entity) => new ResourceObject(
                $entity->getPerscomId(),
                $this->normalizer->normalize($entity, 'perscom_array', ['action' => 'update']),
            ),
            $entities,
        );
        $resource->batchUpdate($resources);
    }

    /**
     * @var array<PerscomEntityInterface> $entities
     */
    public function batchUpdateSeq(ResourceContract $resource, array $entities): void
    {
        foreach ($entities as $entity) {
            $resource->update(
                $entity->getPerscomId(),
                $this->normalizer->normalize($entity, 'perscom_array', ['action' => 'update']),
            );
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
        usleep(100000); // 100ms cooldown to avoid rate limits :)

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

                $existingItem = $existingItems[$item['id']] ?? null;
                try {
                    $obj = $this->normalizer->denormalize($item, $entityClass, 'perscom_array', [
                        AbstractNormalizer::OBJECT_TO_POPULATE => $existingItem,
                        ...$context,
                    ]);

                    $this->em->persist($obj);
                    $existingItems[$item['id']] = $obj;
                } catch (Exception) {
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

        return $allItems;
    }

    /**
     * @param PerscomEntityInterface[] $items
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
     * @param array<T>
     * @return array<class-string<T>, T[]>
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
        return !$this->isRunning && ($this->settingRepository->get('perscom.sync.enabled') ?? true);
    }
}
