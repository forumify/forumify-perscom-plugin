<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250816090815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'fix nullable on deprecated perscom_user_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_mission_rsvp CHANGE perscom_user_id perscom_user_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_mission_rsvp CHANGE perscom_user_id perscom_user_id INT NOT NULL');
    }
}
