<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250815091948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add perscom user id to notifications';
    }

    public function up(Schema $schema): void
    {
        $userIdMap = $this->connection
            ->executeQuery('SELECT id, user_id FROM perscom_user WHERE user_id IS NOT NULL')
            ->fetchAllAssociative();
        $userIdMap = array_combine(array_column($userIdMap, 'user_id'), array_column($userIdMap, 'id'));

        $recordNotificationIterator = $this->connection
            ->executeQuery('SELECT id, recipient_id, context FROM notification WHERE type = ?', ['perscom_new_record'])
            ->iterateAssociative();

        foreach ($recordNotificationIterator as $notification) {
            $userId = $userIdMap[$notification['recipient_id']] ?? null;
            if ($userId === null) {
                continue;
            }

            $context = json_decode($notification['context'], true);
            if ($context === null) {
                continue;
            }
            $context['user'] = $userId;
            $this->addSql('UPDATE notification SET context = :context WHERE id = :id', [
                'context' => json_encode($context),
                'id' => $notification['id'],
            ]);
        }
    }

    public function down(Schema $schema): void
    {
    }
}
