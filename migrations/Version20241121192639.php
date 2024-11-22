<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241121192639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add unit position and unique constraint to aars';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_after_action_report ADD unit_position INT DEFAULT 100 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX unit_mission_uniq ON perscom_after_action_report (mission_id, unit_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX unit_mission_uniq ON perscom_after_action_report');
        $this->addSql('ALTER TABLE perscom_after_action_report DROP unit_position');
    }
}
