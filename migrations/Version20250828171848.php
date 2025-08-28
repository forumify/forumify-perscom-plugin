<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250828171848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'removed unused perscom columns on form fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_D963AE58F58CDBCA ON perscom_form_field');
        $this->addSql('ALTER TABLE perscom_form_field DROP perscom_id, DROP dirty');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_form_field ADD perscom_id INT DEFAULT NULL, ADD dirty TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D963AE58F58CDBCA ON perscom_form_field (perscom_id)');
    }
}
