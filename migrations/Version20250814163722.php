<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250814163722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'allow for longer field keys/names';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_form_field CHANGE `key` `key` VARCHAR(255) NOT NULL, CHANGE type type VARCHAR(64) NOT NULL, CHANGE label label VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_form_field CHANGE `key` `key` VARCHAR(64) NOT NULL, CHANGE type type VARCHAR(32) NOT NULL, CHANGE label label VARCHAR(128) NOT NULL');
    }
}
