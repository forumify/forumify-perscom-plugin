<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Service;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AwardRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\CombatRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\QualificationRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RecordInterface;
use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;
use Forumify\PerscomPlugin\Perscom\Event\RecordsCreatedEvent;
use Forumify\PerscomPlugin\Perscom\Exception\PerscomException;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RecordService
{
    public function __construct(
        private readonly PerscomUserService $perscomUserService,
        private readonly EventDispatcherInterface $eventDispatcher,
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

        $class = self::typeToClass($type);

        $records = [];
        foreach ($users as $user) {
            /** @var RecordInterface $record */
            $record = new $class();
            $record->setUser($user);
            $record->setText($data['text'] ?? '');
            $record->setCreatedAt($data['created_at']);
            $record->setDocument($data['document'] ?? null);

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
     * @param array<RecordInterface> $records
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
    public static function typeToClass(string $type): string
    {
        return match ($type) {
            'service' => ServiceRecord::class,
            'award' => AwardRecord::class,
            'assignment' => AssignmentRecord::class,
            'combat' => CombatRecord::class,
            'rank' => RankRecord::class,
            'qualification' => QualificationRecord::class,
            default => ServiceRecord::class,
        };
    }

    public static function classToType(RecordInterface $record): string
    {
        $class = ClassUtils::getRealClass(get_class($record));

        return match ($class) {
            ServiceRecord::class => 'service',
            AwardRecord::class => 'award',
            AssignmentRecord::class => 'assignment',
            CombatRecord::class => 'combat',
            RankRecord::class => 'rank',
            QualificationRecord::class => 'qualification',
            default => 'service',
        };
    }
}
