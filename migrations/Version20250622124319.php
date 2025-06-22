<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250622124319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add forms and submissions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_form (id INT AUTO_INCREMENT NOT NULL, default_status_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, success_message LONGTEXT NOT NULL, description LONGTEXT NOT NULL, instructions LONGTEXT NOT NULL, fields JSON NOT NULL, perscom_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1BF12ACDF58CDBCA (perscom_id), INDEX IDX_1BF12ACDFA95281A (default_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_form_submission (id INT AUTO_INCREMENT NOT NULL, form_id INT DEFAULT NULL, user_id INT DEFAULT NULL, status_id INT DEFAULT NULL, data JSON DEFAULT NULL, perscom_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_2A50A241F58CDBCA (perscom_id), INDEX IDX_2A50A2415FF69B7D (form_id), INDEX IDX_2A50A241A76ED395 (user_id), INDEX IDX_2A50A2416BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form ADD CONSTRAINT FK_1BF12ACDFA95281A FOREIGN KEY (default_status_id) REFERENCES perscom_status (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form_submission ADD CONSTRAINT FK_2A50A2415FF69B7D FOREIGN KEY (form_id) REFERENCES perscom_form (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form_submission ADD CONSTRAINT FK_2A50A241A76ED395 FOREIGN KEY (user_id) REFERENCES perscom_user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form_submission ADD CONSTRAINT FK_2A50A2416BF700BD FOREIGN KEY (status_id) REFERENCES perscom_status (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form_submission ADD status_reason LONGTEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form CHANGE fields fields JSON DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form DROP FOREIGN KEY FK_1BF12ACDFA95281A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form_submission DROP FOREIGN KEY FK_2A50A2415FF69B7D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form_submission DROP FOREIGN KEY FK_2A50A241A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form_submission DROP FOREIGN KEY FK_2A50A2416BF700BD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_form
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_form_submission
        SQL);
    }
}
