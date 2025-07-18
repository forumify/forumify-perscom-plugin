<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250712083926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'prepare for doctrine dbal/orm update';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_rank CHANGE abbreviation abbreviation VARCHAR(8) NOT NULL, CHANGE paygrade paygrade VARCHAR(16) NOT NULL');
        $this->addSql('ALTER TABLE perscom_record_assignment CHANGE type type VARCHAR(16) NOT NULL');
        $this->addSql('ALTER TABLE perscom_record_rank CHANGE type type VARCHAR(16) NOT NULL');
        $this->addSql('ALTER TABLE perscom_specialty CHANGE abbreviation abbreviation VARCHAR(8) NOT NULL');
        $this->addSql('ALTER TABLE perscom_status CHANGE color color CHAR(7) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_rank CHANGE abbreviation abbreviation VARCHAR(255) NOT NULL, CHANGE paygrade paygrade VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE perscom_record_assignment CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE perscom_record_rank CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE perscom_specialty CHANGE abbreviation abbreviation VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE perscom_status CHANGE color color VARCHAR(255) NOT NULL');
    }
}
