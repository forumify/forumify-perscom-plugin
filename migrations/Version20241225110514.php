<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241225110514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add perscom <-> forumify user map';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE perscom_user (id INT NOT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_C4EA01CBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE perscom_user ADD CONSTRAINT FK_C4EA01CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_user DROP FOREIGN KEY FK_C4EA01CBA76ED395');
        $this->addSql('DROP TABLE perscom_user');
    }
}
