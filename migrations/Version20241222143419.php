<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241222143419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add calendar integration to courses';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_course_class ADD calendar_id INT DEFAULT NULL, ADD event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_course_class ADD CONSTRAINT FK_254C72AA40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)');
        $this->addSql('ALTER TABLE perscom_course_class ADD CONSTRAINT FK_254C72A71F7E88B FOREIGN KEY (event_id) REFERENCES calendar_event (id)');
        $this->addSql('CREATE INDEX IDX_254C72AA40A2C8 ON perscom_course_class (calendar_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_254C72A71F7E88B ON perscom_course_class (event_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_course_class DROP FOREIGN KEY FK_254C72AA40A2C8');
        $this->addSql('ALTER TABLE perscom_course_class DROP FOREIGN KEY FK_254C72A71F7E88B');
        $this->addSql('DROP INDEX IDX_254C72AA40A2C8 ON perscom_course_class');
        $this->addSql('DROP INDEX UNIQ_254C72A71F7E88B ON perscom_course_class');
        $this->addSql('ALTER TABLE perscom_course_class DROP calendar_id, DROP event_id');
    }
}
