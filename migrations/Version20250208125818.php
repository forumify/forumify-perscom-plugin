<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250208125818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add mission RSVP';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE perscom_mission_rsvp (perscom_user_id INT NOT NULL, mission_id INT NOT NULL, going TINYINT(1) NOT NULL, INDEX IDX_B053BB1CBE6CAE90 (mission_id), PRIMARY KEY(perscom_user_id, mission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE perscom_mission_rsvp ADD CONSTRAINT FK_B053BB1CBE6CAE90 FOREIGN KEY (mission_id) REFERENCES perscom_mission (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_mission_rsvp DROP FOREIGN KEY FK_B053BB1CBE6CAE90');
        $this->addSql('DROP TABLE perscom_mission_rsvp');
    }
}
