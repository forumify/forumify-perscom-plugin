<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260115200506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'makes abbreviation nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_specialty CHANGE abbreviation abbreviation VARCHAR(8) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_specialty CHANGE abbreviation abbreviation VARCHAR(8) NOT NULL');
    }
}
