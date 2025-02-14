<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250214214530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'fix foreign key on perscom user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_user DROP FOREIGN KEY FK_C4EA01CBA76ED395');
        $this->addSql('ALTER TABLE perscom_user ADD CONSTRAINT FK_C4EA01CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_user DROP FOREIGN KEY FK_C4EA01CBA76ED395');
        $this->addSql('ALTER TABLE perscom_user ADD CONSTRAINT FK_C4EA01CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
