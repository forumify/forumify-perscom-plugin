<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250718160041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'clear perscom settings';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('REPLACE INTO `setting` (`key`, `value`) VALUES (?, ?)', ['perscom.endpoint', 'null']);
        $this->addSql('DELETE FROM `setting` WHERE `key` = "perscom.perscom_id"');
    }

    public function down(Schema $schema): void
    {
    }
}
