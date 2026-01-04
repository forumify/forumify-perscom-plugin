<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260104120026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add unit supervisors';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE perscom_unit_supervisors (unit_id INT NOT NULL, position_id INT NOT NULL, INDEX IDX_E1B759D8F8BD700D (unit_id), INDEX IDX_E1B759D8DD842E46 (position_id), PRIMARY KEY (unit_id, position_id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE perscom_unit_supervisors ADD CONSTRAINT FK_E1B759D8F8BD700D FOREIGN KEY (unit_id) REFERENCES perscom_unit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perscom_unit_supervisors ADD CONSTRAINT FK_E1B759D8DD842E46 FOREIGN KEY (position_id) REFERENCES perscom_position (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perscom_unit ADD mark_supervisors_on_roster TINYINT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_unit_supervisors DROP FOREIGN KEY FK_E1B759D8F8BD700D');
        $this->addSql('ALTER TABLE perscom_unit_supervisors DROP FOREIGN KEY FK_E1B759D8DD842E46');
        $this->addSql('DROP TABLE perscom_unit_supervisors');
        $this->addSql('ALTER TABLE perscom_unit DROP mark_supervisors_on_roster');
    }
}
