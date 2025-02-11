<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250208130249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add timestamps to rsvps';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_mission_rsvp ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_mission_rsvp DROP created_at, DROP updated_at');
    }
}
