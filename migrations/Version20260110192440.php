<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260110192440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add roles to position, specialty, status and unit';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_position ADD role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_position ADD CONSTRAINT FK_7847827BD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_7847827BD60322AC ON perscom_position (role_id)');
        $this->addSql('ALTER TABLE perscom_specialty ADD role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_specialty ADD CONSTRAINT FK_EA5863ADD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_EA5863ADD60322AC ON perscom_specialty (role_id)');
        $this->addSql('ALTER TABLE perscom_status ADD role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_status ADD CONSTRAINT FK_6A0291DBD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6A0291DBD60322AC ON perscom_status (role_id)');
        $this->addSql('ALTER TABLE perscom_unit ADD role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_unit ADD CONSTRAINT FK_95C2DBD1D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_95C2DBD1D60322AC ON perscom_unit (role_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_position DROP FOREIGN KEY FK_7847827BD60322AC');
        $this->addSql('DROP INDEX IDX_7847827BD60322AC ON perscom_position');
        $this->addSql('ALTER TABLE perscom_position DROP role_id');
        $this->addSql('ALTER TABLE perscom_specialty DROP FOREIGN KEY FK_EA5863ADD60322AC');
        $this->addSql('DROP INDEX IDX_EA5863ADD60322AC ON perscom_specialty');
        $this->addSql('ALTER TABLE perscom_specialty DROP role_id');
        $this->addSql('ALTER TABLE perscom_status DROP FOREIGN KEY FK_6A0291DBD60322AC');
        $this->addSql('DROP INDEX IDX_6A0291DBD60322AC ON perscom_status');
        $this->addSql('ALTER TABLE perscom_status DROP role_id');
        $this->addSql('ALTER TABLE perscom_unit DROP FOREIGN KEY FK_95C2DBD1D60322AC');
        $this->addSql('DROP INDEX IDX_95C2DBD1D60322AC ON perscom_unit');
        $this->addSql('ALTER TABLE perscom_unit DROP role_id');
    }
}
