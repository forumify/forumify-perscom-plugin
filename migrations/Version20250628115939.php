<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250628115939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add dirty flags to perscom entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_award ADD dirty TINYINT(1) DEFAULT 0 NOT NULL, ADD image_dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_document ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form_submission ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_position ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_qualification ADD dirty TINYINT(1) DEFAULT 0 NOT NULL, ADD image_dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_rank ADD dirty TINYINT(1) DEFAULT 0 NOT NULL, ADD image_dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_roster ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_specialty ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_status ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_unit ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user ADD signature_dirty TINYINT(1) DEFAULT 1 NOT NULL, ADD uniform_dirty TINYINT(1) DEFAULT 1 NOT NULL, ADD dirty TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_award DROP dirty, DROP image_dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_document DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_form_submission DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_position DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_qualification DROP dirty, DROP image_dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_rank DROP dirty, DROP image_dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_roster DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_specialty DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_status DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_unit DROP dirty
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user DROP signature_dirty, DROP uniform_dirty, DROP dirty
        SQL);
    }
}
