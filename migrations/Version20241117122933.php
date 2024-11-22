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
    }

    public function down(Schema $schema): void
    {
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
