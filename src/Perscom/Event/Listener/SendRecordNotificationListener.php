<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event\Listener;

use Forumify\Core\Entity\Notification;
use Forumify\Core\Notification\NotificationService;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AwardRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\QualificationRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RecordInterface;
use Forumify\PerscomPlugin\Perscom\Event\RecordsCreatedEvent;
use Forumify\PerscomPlugin\Perscom\Notification\NewRecordNotificationType;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsEventListener]
class SendRecordNotificationListener
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function __invoke(RecordsCreatedEvent $event): void
    {
        if (!$event->sendNotification) {
            return;
        }

        foreach ($event->records as $record) {
            $this->sendNotification($record);
        }
    }

    private function sendNotification(RecordInterface $record): void
    {
        $user = $record->getUser()->getUser();
        if ($user === null) {
            return;
        }

        // NOTE: backwards compatibility conversion to preserve NewRecordNotificationType
        $type = RecordService::classToType($record);
        $data = $this->normalizer->normalize($record, 'perscom_array');

        if ($record instanceof AwardRecord) {
            $data['award']['name'] = $record->getAward()->getName();
        } elseif ($record instanceof RankRecord) {
            $data['rank']['name'] = $record->getRank()->getName();
        } elseif ($record instanceof AssignmentRecord) {
            $data['position']['name'] = $record->getPosition()->getName();
            $data['unit']['name'] = $record->getUnit()->getName();
        } elseif ($record instanceof QualificationRecord) {
            $data['qualification']['name'] = $record->getQualification()->getName();
        }

        $this->notificationService->sendNotification(new Notification(
            NewRecordNotificationType::TYPE,
            $user,
            [
                'data' => $data,
                'type' => $type,
            ]
        ));
    }
}
