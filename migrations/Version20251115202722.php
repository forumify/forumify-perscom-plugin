<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Forumify\PerscomPlugin\Perscom\Entity\Form;

final class Version20251115202722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add default ACL permission for backwards compatibility';
    }

    public function up(Schema $schema): void
    {
        $allFormIds = $this->connection->executeQuery('SELECT id FROM perscom_form')->fetchFirstColumn();
        if (empty($allFormIds)) {
            return;
        }

        $userRoleId = $this->connection->executeQuery('SELECT id FROM role WHERE slug = ?', ['user'])->fetchFirstColumn();
        $userRoleId = reset($userRoleId);
        if ($userRoleId === false) {
            return;
        }

        foreach ($allFormIds as $formId) {
            $this->addSql('INSERT INTO acl (entity_id, entity, permission) VALUES (?, ?, ?)', [
                $formId,
                Form::class,
                'create_submissions',
            ]);
            $this->addSql('INSERT INTO acl_role (acl, role) VALUES (LAST_INSERT_ID(), ?)', [$userRoleId]);
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM acl WHERE entity = ? AND permission = ?', [Form::class, 'create_submissions']);
    }
}
