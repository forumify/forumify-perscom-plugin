<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Service;

use Exception;
use Forumify\PerscomPlugin\Perscom\Event\RecordsCreatedEvent;
use Forumify\PerscomPlugin\Perscom\Exception\PerscomException;
use Forumify\PerscomPlugin\Perscom\Exception\PerscomUserNotFoundException;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Perscom\Data\ResourceObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RecordService
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly PerscomUserService $perscomUserService,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @throws PerscomException
     */
    public function createRecord(string $type, array $data): void
    {
        $sendNotification = $data['sendNotification'] ?? false;
        unset($data['sendNotification']);

        $userIds = $data['users'] ?? [];
        unset($data['users']);

        if (empty($userIds)) {
            return;
        }

        $resources = array_map(fn (int|string $userId) => new ResourceObject(null, [
            'user_id' => (int)$userId,
            'author_id' => $this->getAuthorId(),
            ...$data,
        ]), $userIds);

        $this->batchCreate($type, $resources, $sendNotification);
    }

    /**
     * @throws PerscomException
     */
    public function createRecords(string $type, array $records, bool $sendNotification): void
    {
        if (empty($records)) {
            return;
        }

        $resources = array_map(fn (array $data) => new ResourceObject(null, [
            'author_id' => $this->getAuthorId(),
            ...$data,
        ]), $records);

        $this->batchCreate($type, $resources, $sendNotification);
    }

    private function getAuthorId(): int
    {
        $author = $this->perscomUserService->getLoggedInPerscomUser();
        if ($author === null || empty($author['id'])) {
            throw new PerscomUserNotFoundException();
        }
        return $author['id'];
    }

    /**
     * @param array<ResourceObject> $records
     * @return void
     * @throws PerscomException
     */
    private function batchCreate(string $type, array $resources, bool $sendNotification): void
    {
        $perscom = $this->perscomFactory->getPerscom();
        $recordResource = match ($type) {
            'service' => $perscom->serviceRecords(),
            'award' => $perscom->awardRecords(),
            'combat' => $perscom->combatRecords(),
            'rank' => $perscom->rankRecords(),
            'assignment' => $perscom->assignmentRecords(),
            'qualification' => $perscom->qualificationRecords()
        };

        try {
            $responses = $recordResource->batchCreate($resources)->json('data');
        } catch (Exception $ex) {
            throw new PerscomException($ex->getMessage(), 0, $ex);
        }

        $this->eventDispatcher->dispatch(new RecordsCreatedEvent($type, $responses, $sendNotification));
    }
}
