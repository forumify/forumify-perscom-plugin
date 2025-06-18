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
use Perscom\Contracts\ResourceContract;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SyncService
{
    private readonly Perscom $perscom;

    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly EntityManagerInterface $em,
        private readonly NormalizerInterface&DenormalizerInterface $normalizer,
        PerscomFactory $perscomFactory,
    ) {
        $this->perscom = $perscomFactory->getPerscom(true);
    }

    public function sync(): void
    {
        $isSyncEnabled = $this->settingRepository->get('perscom.sync.enabled') ?? true;
        if (!$isSyncEnabled) {
            return;
        }

        $p = $this->perscom;
        $awards = $this->fullSyncEntity($p->awards(), Entity\Award::class, ['image']);
        $documents = $this->fullSyncEntity($p->documents(), Entity\Document::class);
        $positions = $this->fullSyncEntity($p->positions(), Entity\Position::class);
        $qualifications = $this->fullSyncEntity($p->qualifications(), Entity\Qualification::class, ['image']);
        $ranks = $this->fullSyncEntity($p->ranks(), Entity\Rank::class, ['image']);
        $specialties = $this->fullSyncEntity($p->specialties(), Entity\Specialty::class);
        $statuses = $this->fullSyncEntity($p->statuses(), Entity\Status::class);
        $units = $this->fullSyncEntity($p->units(), Entity\Unit::class);
        $this->fullSyncEntity($p->groups(), Entity\Roster::class, ['units'], context: ['units' => $units]);
        $users = $this->fullSyncEntity($p->users(), Entity\PerscomUser::class, context: [
            'positions' => $positions,
            'ranks' => $ranks,
            'specialties' => $specialties,
            'statuses' => $statuses,
            'units' => $units,
        ]);
        $this->fullSyncEntity(
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
        $this->fullSyncEntity(
            $p->awardRecords(),
            Entity\Record\AwardRecord::class,
            context: [
                'users' => $users,
                'documents' => $documents,
                'awards' => $awards,
            ],
            batchSize: 1000,
        );
        $this->fullSyncEntity(
            $p->combatRecords(),
            Entity\Record\CombatRecord::class,
            context: [
                'users' => $users,
                'documents' => $documents,
            ],
            batchSize: 1000,
        );
        $this->fullSyncEntity(
            $p->qualificationRecords(),
            Entity\Record\QualificationRecord::class,
            context: [
                'users' => $users,
                'documents' => $documents,
                'qualifications' => $qualifications,
            ],
            batchSize: 1000,
        );
        $this->fullSyncEntity(
            $p->rankRecords(),
            Entity\Record\RankRecord::class,
            context: [
                'users' => $users,
                'documents' => $documents,
                'ranks' => $ranks,
            ],
            batchSize: 1000,
        );
        $this->fullSyncEntity(
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
    }

    /**
     * @template T of object
     * @param class-string<T> $entityClass
     * @return array<int, T> all entities indexed by Perscom ID
     */
    private function fullSyncEntity(
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
     * @param Entity\PerscomEntityInterface[] $items
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
}
