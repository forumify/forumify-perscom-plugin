<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241117194153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add after action report fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_after_action_report ADD mission_id INT DEFAULT NULL, ADD unit_id INT NOT NULL, ADD unit_name VARCHAR(255) NOT NULL, ADD report LONGTEXT NOT NULL, ADD attendance JSON NOT NULL');
        $this->addSql('ALTER TABLE perscom_after_action_report ADD CONSTRAINT FK_BA443E50BE6CAE90 FOREIGN KEY (mission_id) REFERENCES perscom_mission (id)');
        $this->addSql('CREATE INDEX IDX_BA443E50BE6CAE90 ON perscom_after_action_report (mission_id)');
        $this->addSql('ALTER TABLE perscom_operation ADD content LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_after_action_report DROP FOREIGN KEY FK_BA443E50BE6CAE90');
        $this->addSql('DROP INDEX IDX_BA443E50BE6CAE90 ON perscom_after_action_report');
        $this->addSql('ALTER TABLE perscom_after_action_report DROP mission_id, DROP unit_id, DROP unit_name, DROP report, DROP attendance');
        $this->addSql('ALTER TABLE perscom_operation DROP content');
    }
}
