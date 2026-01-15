<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260115160839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add option to control name on enlistment form';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO setting (`key`, `value`) VALUES (?, ?)', [
            'perscom.enlistment.roleplay_names',
            'true',
        ]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM setting WHERE `key` = ?', ['perscom.enlistment.roleplay_names']);
    }
}
