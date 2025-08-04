<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250719094640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add form fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE perscom_form_field (id INT AUTO_INCREMENT NOT NULL, form_id INT DEFAULT NULL, `key` VARCHAR(64) NOT NULL, type VARCHAR(32) NOT NULL, label VARCHAR(128) NOT NULL, help LONGTEXT NOT NULL, required TINYINT(1) NOT NULL, readonly TINYINT(1) NOT NULL, options JSON DEFAULT NULL, position INT NOT NULL, INDEX IDX_D963AE585FF69B7D (form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE perscom_form_field ADD CONSTRAINT FK_D963AE585FF69B7D FOREIGN KEY (form_id) REFERENCES perscom_form (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perscom_form DROP fields');
        $this->addSql('ALTER TABLE perscom_form_field ADD perscom_id INT DEFAULT NULL, ADD dirty TINYINT(1) DEFAULT 0 NOT NULL, ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D963AE58F58CDBCA ON perscom_form_field (perscom_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D963AE585FF69B7D4E645A7E ON perscom_form_field (form_id, `key`)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_D963AE58F58CDBCA ON perscom_form_field');
        $this->addSql('DROP INDEX UNIQ_D963AE585FF69B7D4E645A7E ON perscom_form_field');
        $this->addSql('ALTER TABLE perscom_form_field DROP perscom_id, DROP dirty, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE perscom_form_field DROP FOREIGN KEY FK_D963AE585FF69B7D');
        $this->addSql('DROP TABLE perscom_form_field');
        $this->addSql('ALTER TABLE perscom_form ADD fields JSON DEFAULT NULL');
    }
}
