<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241222140021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add calendar integration to missions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_mission ADD calendar_id INT DEFAULT NULL, ADD calendar_event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_mission ADD CONSTRAINT FK_9576A7DBA40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)');
        $this->addSql('ALTER TABLE perscom_mission ADD CONSTRAINT FK_9576A7DB7495C8E3 FOREIGN KEY (calendar_event_id) REFERENCES calendar_event (id)');
        $this->addSql('CREATE INDEX IDX_9576A7DBA40A2C8 ON perscom_mission (calendar_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9576A7DB7495C8E3 ON perscom_mission (calendar_event_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_mission DROP FOREIGN KEY FK_9576A7DBA40A2C8');
        $this->addSql('ALTER TABLE perscom_mission DROP FOREIGN KEY FK_9576A7DB7495C8E3');
        $this->addSql('DROP INDEX IDX_9576A7DBA40A2C8 ON perscom_mission');
        $this->addSql('DROP INDEX UNIQ_9576A7DB7495C8E3 ON perscom_mission');
        $this->addSql('ALTER TABLE perscom_mission DROP calendar_id, DROP calendar_event_id');
    }
}
