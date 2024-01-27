<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240122220307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add perscom enlistment topics';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE perscom_enlistment_topic (submission_id INT NOT NULL, user_id INT DEFAULT NULL, topic_id INT DEFAULT NULL, INDEX IDX_A96B7362A76ED395 (user_id), UNIQUE INDEX UNIQ_A96B73621F55203D (topic_id), PRIMARY KEY(submission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE perscom_enlistment_topic ADD CONSTRAINT FK_A96B7362A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perscom_enlistment_topic ADD CONSTRAINT FK_A96B73621F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE perscom_enlistment_topic DROP FOREIGN KEY FK_A96B7362A76ED395');
        $this->addSql('ALTER TABLE perscom_enlistment_topic DROP FOREIGN KEY FK_A96B73621F55203D');
        $this->addSql('DROP TABLE perscom_enlistment_topic');
    }
}
