<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250621201416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add perscom sync results';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_sync_result (id INT AUTO_INCREMENT NOT NULL, start DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', end DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', success TINYINT(1) NOT NULL, error_message LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_sync_result
        SQL);
    }
}
