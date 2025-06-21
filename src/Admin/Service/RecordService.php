<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AwardRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\CombatRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\QualificationRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RecordInterface;
use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;
use Forumify\PerscomPlugin\Perscom\Event\RecordsCreatedEvent;
use Forumify\PerscomPlugin\Perscom\Exception\PerscomException;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Perscom\Data\ResourceObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RecordService
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly PerscomUserService $perscomUserService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws PerscomException
     */
    public function createRecord(string $type, array $data): void
    {
        $sendNotification = $data['sendNotification'] ?? false;
        $users = $data['users'] ?? [];
        if (empty($users)) {
            return;
        }

        $data['created_at'] = $data['created_at']->format(Perscom::DATE_FORMAT);

        $class = $this->typeToClass($type);

        $records = [];
        foreach ($users as $user) {
            /** @var RecordInterface $record */
            $record = new $class();
            $record->setUser($user);
            $record->setText($data['text'] ?? '');

            if ($record instanceof AwardRecord) {
                $record->setAward($data['award']);
            } elseif ($record instanceof AssignmentRecord) {
                $record->setType($data['type']);
                $record->setStatus($data['status']);
                $record->setSpecialty($data['specialty']);
                $record->setUnit($data['unit']);
                $record->setPosition($data['position']);
            } elseif ($record instanceof RankRecord) {
                $record->setType($data['type']);
                $record->setRank($data['rank']);
            } elseif ($record instanceof QualificationRecord) {
                $record->setQualification($data['qualification']);
            }

            $records[] = $record;
        }
        $this->createRecords($records, $sendNotification);
    }

    /**
     * @param RecordInterface[] $records
     */
    public function createRecords(array $records, bool $sendNotification): void
    {
        if (empty($records)) {
            return;
        }

        $author = $this->perscomUserService->getLoggedInPerscomUser();
        foreach ($records as $record) {
            if ($record->getAuthor() === null) {
                $record->setAuthor($author);
            }
            $this->entityManager->persist($record);
        }
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new RecordsCreatedEvent($records, $sendNotification));
    }

    /**
     * @return class-string<RecordInterface>
     */
    private function typeToClass(string $type): string
    {
        return match ($type) {
            'service' => ServiceRecord::class,
            'award' => AwardRecord::class,
            'assignment' => AssignmentRecord::class,
            'combat' => CombatRecord::class,
            'rank' => RankRecord::class,
            'qualification' => QualificationRecord::class,
        };
    }
}
