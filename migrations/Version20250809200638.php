<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250809200638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add SteamID to users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_user ADD steam_id BIGINT UNSIGNED DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_user DROP steam_id');
    }
}
