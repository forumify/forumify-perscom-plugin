<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241117122933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add operations';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE perscom_after_action_report (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_BA443E50DE12AB56 (created_by), INDEX IDX_BA443E5016FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE perscom_mission (id INT AUTO_INCREMENT NOT NULL, operation_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, title VARCHAR(255) NOT NULL, briefing LONGTEXT NOT NULL, start DATETIME NOT NULL, end DATETIME DEFAULT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_9576A7DB989D9B62 (slug), INDEX IDX_9576A7DB44AC3583 (operation_id), INDEX IDX_9576A7DBDE12AB56 (created_by), INDEX IDX_9576A7DB16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE perscom_operation (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, start DATE DEFAULT NULL, end DATE DEFAULT NULL, mission_briefing_template LONGTEXT NOT NULL, after_action_report_template LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_13BF632C989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE perscom_after_action_report ADD CONSTRAINT FK_BA443E50DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE perscom_after_action_report ADD CONSTRAINT FK_BA443E5016FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE perscom_mission ADD CONSTRAINT FK_9576A7DB44AC3583 FOREIGN KEY (operation_id) REFERENCES perscom_operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perscom_mission ADD CONSTRAINT FK_9576A7DBDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE perscom_mission ADD CONSTRAINT FK_9576A7DB16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE perscom_after_action_report ADD mission_id INT DEFAULT NULL, ADD unit_id INT NOT NULL, ADD unit_name VARCHAR(255) NOT NULL, ADD report LONGTEXT NOT NULL, ADD attendance JSON NOT NULL');
        $this->addSql('ALTER TABLE perscom_after_action_report ADD CONSTRAINT FK_BA443E50BE6CAE90 FOREIGN KEY (mission_id) REFERENCES perscom_mission (id)');
        $this->addSql('CREATE INDEX IDX_BA443E50BE6CAE90 ON perscom_after_action_report (mission_id)');
        $this->addSql('ALTER TABLE perscom_operation ADD content LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE perscom_after_action_report ADD unit_position INT DEFAULT 100 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX unit_mission_uniq ON perscom_after_action_report (mission_id, unit_id)');
        $this->addSql('INSERT INTO setting VALUES(?, ?)', ['perscom.operations.attendance_states', '["present", "excused", "absent"]']);
        $this->addSql('ALTER TABLE perscom_mission ADD send_notification TINYINT(1) DEFAULT 0 NOT NULL, ADD create_combat_records TINYINT(1) DEFAULT 0 NOT NULL, ADD combat_record_text VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_mission DROP send_notification, DROP create_combat_records, DROP combat_record_text');
        $this->addSql('DROP INDEX unit_mission_uniq ON perscom_after_action_report');
        $this->addSql('ALTER TABLE perscom_after_action_report DROP unit_position');
        $this->addSql('ALTER TABLE perscom_after_action_report DROP FOREIGN KEY FK_BA443E50BE6CAE90');
        $this->addSql('DROP INDEX IDX_BA443E50BE6CAE90 ON perscom_after_action_report');
        $this->addSql('ALTER TABLE perscom_after_action_report DROP mission_id, DROP unit_id, DROP unit_name, DROP report, DROP attendance');
        $this->addSql('ALTER TABLE perscom_operation DROP content');
        $this->addSql('ALTER TABLE perscom_after_action_report DROP FOREIGN KEY FK_BA443E50DE12AB56');
        $this->addSql('ALTER TABLE perscom_after_action_report DROP FOREIGN KEY FK_BA443E5016FE72E1');
        $this->addSql('ALTER TABLE perscom_mission DROP FOREIGN KEY FK_9576A7DB44AC3583');
        $this->addSql('ALTER TABLE perscom_mission DROP FOREIGN KEY FK_9576A7DBDE12AB56');
        $this->addSql('ALTER TABLE perscom_mission DROP FOREIGN KEY FK_9576A7DB16FE72E1');
        $this->addSql('DROP TABLE perscom_after_action_report');
        $this->addSql('DROP TABLE perscom_mission');
        $this->addSql('DROP TABLE perscom_operation');
    }
}
