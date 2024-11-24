<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241123195746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add report in';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE perscom_report_in (perscom_user_id INT NOT NULL, last_report_in_date DATETIME NOT NULL, previous_status_id INT DEFAULT NULL, PRIMARY KEY(perscom_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE perscom_after_action_report DROP FOREIGN KEY FK_BA443E50BE6CAE90');
        $this->addSql('ALTER TABLE perscom_after_action_report ADD CONSTRAINT FK_BA443E50BE6CAE90 FOREIGN KEY (mission_id) REFERENCES perscom_mission (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE perscom_report_in');
        $this->addSql('ALTER TABLE perscom_after_action_report DROP FOREIGN KEY FK_BA443E50BE6CAE90');
        $this->addSql('ALTER TABLE perscom_after_action_report ADD CONSTRAINT FK_BA443E50BE6CAE90 FOREIGN KEY (mission_id) REFERENCES perscom_mission (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
