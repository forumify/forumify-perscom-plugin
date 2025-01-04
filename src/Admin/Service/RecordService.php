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

        $perscom = $this->perscomFactory->getPerscom();
        $recordResource = match ($type) {
            'service' => $perscom->serviceRecords(),
            'award' => $perscom->awardRecords(),
            'combat' => $perscom->combatRecords(),
            'rank' => $perscom->rankRecords(),
            'assignment' => $perscom->assignmentRecords(),
            'qualification' => $perscom->qualificationRecords()
        };

        $records = $this->dataToResourceObjects($data);
        try {
            $responses = $recordResource->batchCreate($records)->json('data');
        } catch (Exception $ex) {
            throw new PerscomException($ex->getMessage(), 0, $ex);
        }

        $this->eventDispatcher->dispatch(new RecordsCreatedEvent($type, $responses, $sendNotification));
    }

    /**
     * @return array<ResourceObject>
     * @throws PerscomUserNotFoundException
     */
    private function dataToResourceObjects(array $data): array
    {
        $author = $this->perscomUserService->getLoggedInPerscomUser();
        if ($author === null || empty($author['id'])) {
            throw new PerscomUserNotFoundException();
        }

        $userIds = $data['users'] ?? [];
        unset($data['users']);

        return array_map(static fn (int|string $userId) => new ResourceObject(null, [
            'user_id' => (int)$userId,
            'author_id' => $author['id'],
            ...$data,
        ]), $userIds);
    }

}
