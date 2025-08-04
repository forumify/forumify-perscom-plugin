<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250627190708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'make sync result end and success nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_sync_result CHANGE end end DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE success success TINYINT(1) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_sync_result CHANGE end end DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE success success TINYINT(1) NOT NULL
        SQL);
    }
}
