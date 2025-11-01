<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251101122207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add index on sync result start for faster lookup';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_B9876D899F79558F ON perscom_sync_result (start)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_B9876D899F79558F ON perscom_sync_result');
    }
}
