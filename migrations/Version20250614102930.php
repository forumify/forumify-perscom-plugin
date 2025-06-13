<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250614102930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add schema for most PERSCOM entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user DROP FOREIGN KEY FK_C4EA01CBA76ED395
        SQL);
        $this->addSql('TRUNCATE TABLE perscom_user');
        $this->addSql('ALTER TABLE perscom_user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE perscom_user CHANGE id perscom_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_user ADD id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user ADD rank_id INT DEFAULT NULL, ADD unit_id INT DEFAULT NULL,
                ADD position_id INT DEFAULT NULL,
                ADD status_id INT DEFAULT NULL,
                ADD name VARCHAR(255) NOT NULL,
                ADD created_at DATETIME DEFAULT NULL,
                ADD updated_at DATETIME DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_award (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image_id INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, perscom_id INT DEFAULT NULL, position INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_89A4B53CF58CDBCA (perscom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_document (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, content LONGTEXT NOT NULL, perscom_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_E602ECF8F58CDBCA (perscom_id), INDEX IDX_E602ECF8DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_position (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, perscom_id INT DEFAULT NULL, position INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7847827BF58CDBCA (perscom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_qualification (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image_id INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, perscom_id INT DEFAULT NULL, position INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_A2CE695EF58CDBCA (perscom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_rank (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, abbreviation VARCHAR(255) NOT NULL, paygrade VARCHAR(255) NOT NULL, image_id INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, perscom_id INT DEFAULT NULL, position INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_C1003F67F58CDBCA (perscom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_record_assignment (id INT AUTO_INCREMENT NOT NULL, status_id INT DEFAULT NULL, unit_id INT DEFAULT NULL, position_id INT DEFAULT NULL, specialty_id INT DEFAULT NULL, created_by INT DEFAULT NULL, user_id INT NOT NULL, document_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, perscom_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_75527C68F58CDBCA (perscom_id), INDEX IDX_75527C686BF700BD (status_id), INDEX IDX_75527C68F8BD700D (unit_id), INDEX IDX_75527C68DD842E46 (position_id), INDEX IDX_75527C689A353316 (specialty_id), INDEX IDX_75527C68DE12AB56 (created_by), INDEX IDX_75527C68A76ED395 (user_id), INDEX IDX_75527C68C33F7837 (document_id), INDEX IDX_75527C688CDE5729 (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_record_award (id INT AUTO_INCREMENT NOT NULL, award_id INT DEFAULT NULL, created_by INT DEFAULT NULL, user_id INT NOT NULL, document_id INT DEFAULT NULL, text LONGTEXT NOT NULL, perscom_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_DB456E6F58CDBCA (perscom_id), INDEX IDX_DB456E63D5282CF (award_id), INDEX IDX_DB456E6DE12AB56 (created_by), INDEX IDX_DB456E6A76ED395 (user_id), INDEX IDX_DB456E6C33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_record_combat (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, user_id INT NOT NULL, document_id INT DEFAULT NULL, text LONGTEXT NOT NULL, perscom_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_FAD13C76F58CDBCA (perscom_id), INDEX IDX_FAD13C76DE12AB56 (created_by), INDEX IDX_FAD13C76A76ED395 (user_id), INDEX IDX_FAD13C76C33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_record_qualification (id INT AUTO_INCREMENT NOT NULL, qualification_id INT DEFAULT NULL, created_by INT DEFAULT NULL, user_id INT NOT NULL, document_id INT DEFAULT NULL, text LONGTEXT NOT NULL, perscom_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_40EBABC9F58CDBCA (perscom_id), INDEX IDX_40EBABC91A75EE38 (qualification_id), INDEX IDX_40EBABC9DE12AB56 (created_by), INDEX IDX_40EBABC9A76ED395 (user_id), INDEX IDX_40EBABC9C33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_record_rank (id INT AUTO_INCREMENT NOT NULL, rank_id INT DEFAULT NULL, created_by INT DEFAULT NULL, user_id INT NOT NULL, document_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, perscom_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6FA23B74F58CDBCA (perscom_id), INDEX IDX_6FA23B747616678F (rank_id), INDEX IDX_6FA23B74DE12AB56 (created_by), INDEX IDX_6FA23B74A76ED395 (user_id), INDEX IDX_6FA23B74C33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_record_service (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, user_id INT NOT NULL, document_id INT DEFAULT NULL, text LONGTEXT NOT NULL, perscom_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_A658D572F58CDBCA (perscom_id), INDEX IDX_A658D572DE12AB56 (created_by), INDEX IDX_A658D572A76ED395 (user_id), INDEX IDX_A658D572C33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_roster (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, perscom_id INT DEFAULT NULL, position INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_71BB593EF58CDBCA (perscom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_roster_units (roster_id INT NOT NULL, unit_id INT NOT NULL, INDEX IDX_F7BB984E75404483 (roster_id), INDEX IDX_F7BB984EF8BD700D (unit_id), PRIMARY KEY(roster_id, unit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_specialty (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, abbreviation VARCHAR(255) NOT NULL, perscom_id INT DEFAULT NULL, position INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_EA5863ADF58CDBCA (perscom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, perscom_id INT DEFAULT NULL, position INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6A0291DBF58CDBCA (perscom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_unit (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, perscom_id INT DEFAULT NULL, position INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_95C2DBD1F58CDBCA (perscom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_user_secondary_positions (perscom_user_id INT NOT NULL, position_id INT NOT NULL, INDEX IDX_41B91062A27F8672 (perscom_user_id), INDEX IDX_41B91062DD842E46 (position_id), PRIMARY KEY(perscom_user_id, position_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_document ADD CONSTRAINT FK_E602ECF8DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment ADD CONSTRAINT FK_75527C686BF700BD FOREIGN KEY (status_id) REFERENCES perscom_status (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment ADD CONSTRAINT FK_75527C68F8BD700D FOREIGN KEY (unit_id) REFERENCES perscom_unit (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment ADD CONSTRAINT FK_75527C68DD842E46 FOREIGN KEY (position_id) REFERENCES perscom_position (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment ADD CONSTRAINT FK_75527C689A353316 FOREIGN KEY (specialty_id) REFERENCES perscom_specialty (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment ADD CONSTRAINT FK_75527C68DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment ADD CONSTRAINT FK_75527C68A76ED395 FOREIGN KEY (user_id) REFERENCES perscom_user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment ADD CONSTRAINT FK_75527C68C33F7837 FOREIGN KEY (document_id) REFERENCES perscom_document (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award ADD CONSTRAINT FK_DB456E63D5282CF FOREIGN KEY (award_id) REFERENCES perscom_award (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award ADD CONSTRAINT FK_DB456E6DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award ADD CONSTRAINT FK_DB456E6A76ED395 FOREIGN KEY (user_id) REFERENCES perscom_user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award ADD CONSTRAINT FK_DB456E6C33F7837 FOREIGN KEY (document_id) REFERENCES perscom_document (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat ADD CONSTRAINT FK_FAD13C76DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat ADD CONSTRAINT FK_FAD13C76A76ED395 FOREIGN KEY (user_id) REFERENCES perscom_user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat ADD CONSTRAINT FK_FAD13C76C33F7837 FOREIGN KEY (document_id) REFERENCES perscom_document (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification ADD CONSTRAINT FK_40EBABC91A75EE38 FOREIGN KEY (qualification_id) REFERENCES perscom_qualification (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification ADD CONSTRAINT FK_40EBABC9DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification ADD CONSTRAINT FK_40EBABC9A76ED395 FOREIGN KEY (user_id) REFERENCES perscom_user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification ADD CONSTRAINT FK_40EBABC9C33F7837 FOREIGN KEY (document_id) REFERENCES perscom_document (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank ADD CONSTRAINT FK_6FA23B747616678F FOREIGN KEY (rank_id) REFERENCES perscom_rank (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank ADD CONSTRAINT FK_6FA23B74DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank ADD CONSTRAINT FK_6FA23B74A76ED395 FOREIGN KEY (user_id) REFERENCES perscom_user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank ADD CONSTRAINT FK_6FA23B74C33F7837 FOREIGN KEY (document_id) REFERENCES perscom_document (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service ADD CONSTRAINT FK_A658D572DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service ADD CONSTRAINT FK_A658D572A76ED395 FOREIGN KEY (user_id) REFERENCES perscom_user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service ADD CONSTRAINT FK_A658D572C33F7837 FOREIGN KEY (document_id) REFERENCES perscom_document (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_roster_units ADD CONSTRAINT FK_F7BB984E75404483 FOREIGN KEY (roster_id) REFERENCES perscom_roster (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_roster_units ADD CONSTRAINT FK_F7BB984EF8BD700D FOREIGN KEY (unit_id) REFERENCES perscom_unit (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user_secondary_positions ADD CONSTRAINT FK_41B91062A27F8672 FOREIGN KEY (perscom_user_id) REFERENCES perscom_user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user_secondary_positions ADD CONSTRAINT FK_41B91062DD842E46 FOREIGN KEY (position_id) REFERENCES perscom_position (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user ADD CONSTRAINT FK_C4EA01CB7616678F FOREIGN KEY (rank_id) REFERENCES perscom_rank (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user ADD CONSTRAINT FK_C4EA01CBF8BD700D FOREIGN KEY (unit_id) REFERENCES perscom_unit (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user ADD CONSTRAINT FK_C4EA01CBDD842E46 FOREIGN KEY (position_id) REFERENCES perscom_position (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user ADD CONSTRAINT FK_C4EA01CB6BF700BD FOREIGN KEY (status_id) REFERENCES perscom_status (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user ADD CONSTRAINT FK_C4EA01CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_C4EA01CBF58CDBCA ON perscom_user (perscom_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C4EA01CB7616678F ON perscom_user (rank_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C4EA01CBF8BD700D ON perscom_user (unit_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C4EA01CBDD842E46 ON perscom_user (position_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C4EA01CB6BF700BD ON perscom_user (status_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user ADD specialty_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user ADD CONSTRAINT FK_C4EA01CB9A353316 FOREIGN KEY (specialty_id) REFERENCES perscom_specialty (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C4EA01CB9A353316 ON perscom_user (specialty_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user ADD signature VARCHAR(255) DEFAULT NULL, ADD perscom_signature VARCHAR(255) DEFAULT NULL, ADD uniform VARCHAR(255) DEFAULT NULL, ADD perscom_uniform VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment DROP FOREIGN KEY FK_75527C68DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_75527C68DE12AB56 ON perscom_record_assignment
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment CHANGE created_by author_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment ADD CONSTRAINT FK_75527C68F675F31B FOREIGN KEY (author_id) REFERENCES perscom_user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75527C68F675F31B ON perscom_record_assignment (author_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award DROP FOREIGN KEY FK_DB456E6DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_DB456E6DE12AB56 ON perscom_record_award
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award CHANGE created_by author_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award ADD CONSTRAINT FK_DB456E6F675F31B FOREIGN KEY (author_id) REFERENCES perscom_user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DB456E6F675F31B ON perscom_record_award (author_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat DROP FOREIGN KEY FK_FAD13C76DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_FAD13C76DE12AB56 ON perscom_record_combat
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat CHANGE created_by author_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat ADD CONSTRAINT FK_FAD13C76F675F31B FOREIGN KEY (author_id) REFERENCES perscom_user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FAD13C76F675F31B ON perscom_record_combat (author_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification DROP FOREIGN KEY FK_40EBABC9DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_40EBABC9DE12AB56 ON perscom_record_qualification
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification CHANGE created_by author_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification ADD CONSTRAINT FK_40EBABC9F675F31B FOREIGN KEY (author_id) REFERENCES perscom_user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_40EBABC9F675F31B ON perscom_record_qualification (author_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank DROP FOREIGN KEY FK_6FA23B74DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6FA23B74DE12AB56 ON perscom_record_rank
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank CHANGE created_by author_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank ADD CONSTRAINT FK_6FA23B74F675F31B FOREIGN KEY (author_id) REFERENCES perscom_user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6FA23B74F675F31B ON perscom_record_rank (author_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service DROP FOREIGN KEY FK_A658D572DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A658D572DE12AB56 ON perscom_record_service
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service CHANGE created_by author_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service ADD CONSTRAINT FK_A658D572F675F31B FOREIGN KEY (author_id) REFERENCES perscom_user (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A658D572F675F31B ON perscom_record_service (author_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment DROP FOREIGN KEY FK_75527C68F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_75527C68F675F31B ON perscom_record_assignment
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment CHANGE author_id created_by INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment ADD CONSTRAINT FK_75527C68DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75527C68DE12AB56 ON perscom_record_assignment (created_by)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award DROP FOREIGN KEY FK_DB456E6F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_DB456E6F675F31B ON perscom_record_award
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award CHANGE author_id created_by INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award ADD CONSTRAINT FK_DB456E6DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DB456E6DE12AB56 ON perscom_record_award (created_by)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat DROP FOREIGN KEY FK_FAD13C76F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_FAD13C76F675F31B ON perscom_record_combat
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat CHANGE author_id created_by INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat ADD CONSTRAINT FK_FAD13C76DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FAD13C76DE12AB56 ON perscom_record_combat (created_by)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification DROP FOREIGN KEY FK_40EBABC9F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_40EBABC9F675F31B ON perscom_record_qualification
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification CHANGE author_id created_by INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification ADD CONSTRAINT FK_40EBABC9DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_40EBABC9DE12AB56 ON perscom_record_qualification (created_by)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank DROP FOREIGN KEY FK_6FA23B74F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6FA23B74F675F31B ON perscom_record_rank
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank CHANGE author_id created_by INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank ADD CONSTRAINT FK_6FA23B74DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6FA23B74DE12AB56 ON perscom_record_rank (created_by)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service DROP FOREIGN KEY FK_A658D572F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A658D572F675F31B ON perscom_record_service
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service CHANGE author_id created_by INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service ADD CONSTRAINT FK_A658D572DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A658D572DE12AB56 ON perscom_record_service (created_by)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user DROP signature, DROP perscom_signature, DROP uniform, DROP perscom_uniform
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user DROP FOREIGN KEY FK_C4EA01CB9A353316
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C4EA01CB9A353316 ON perscom_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user DROP specialty_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user DROP FOREIGN KEY FK_C4EA01CBDD842E46
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user DROP FOREIGN KEY FK_C4EA01CB7616678F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user DROP FOREIGN KEY FK_C4EA01CB6BF700BD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user DROP FOREIGN KEY FK_C4EA01CBF8BD700D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_document DROP FOREIGN KEY FK_E602ECF8DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment DROP FOREIGN KEY FK_75527C686BF700BD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment DROP FOREIGN KEY FK_75527C68F8BD700D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment DROP FOREIGN KEY FK_75527C68DD842E46
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment DROP FOREIGN KEY FK_75527C689A353316
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment DROP FOREIGN KEY FK_75527C68DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment DROP FOREIGN KEY FK_75527C68A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_assignment DROP FOREIGN KEY FK_75527C68C33F7837
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award DROP FOREIGN KEY FK_DB456E63D5282CF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award DROP FOREIGN KEY FK_DB456E6DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award DROP FOREIGN KEY FK_DB456E6A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_award DROP FOREIGN KEY FK_DB456E6C33F7837
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat DROP FOREIGN KEY FK_FAD13C76DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat DROP FOREIGN KEY FK_FAD13C76A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_combat DROP FOREIGN KEY FK_FAD13C76C33F7837
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification DROP FOREIGN KEY FK_40EBABC91A75EE38
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification DROP FOREIGN KEY FK_40EBABC9DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification DROP FOREIGN KEY FK_40EBABC9A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_qualification DROP FOREIGN KEY FK_40EBABC9C33F7837
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank DROP FOREIGN KEY FK_6FA23B747616678F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank DROP FOREIGN KEY FK_6FA23B74DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank DROP FOREIGN KEY FK_6FA23B74A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_rank DROP FOREIGN KEY FK_6FA23B74C33F7837
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service DROP FOREIGN KEY FK_A658D572DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service DROP FOREIGN KEY FK_A658D572A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_record_service DROP FOREIGN KEY FK_A658D572C33F7837
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_roster_units DROP FOREIGN KEY FK_F7BB984E75404483
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_roster_units DROP FOREIGN KEY FK_F7BB984EF8BD700D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user_secondary_positions DROP FOREIGN KEY FK_41B91062A27F8672
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user_secondary_positions DROP FOREIGN KEY FK_41B91062DD842E46
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_award
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_document
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_position
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_qualification
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_rank
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_record_assignment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_record_award
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_record_combat
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_record_qualification
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_record_rank
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_record_service
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_roster
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_roster_units
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_specialty
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_status
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_unit
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_user_secondary_positions
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user DROP FOREIGN KEY FK_C4EA01CBA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_C4EA01CBF58CDBCA ON perscom_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C4EA01CB7616678F ON perscom_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C4EA01CBF8BD700D ON perscom_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C4EA01CBDD842E46 ON perscom_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C4EA01CB6BF700BD ON perscom_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user DROP rank_id, DROP unit_id, DROP position_id, DROP status_id, DROP name, DROP perscom_id, DROP created_at, DROP updated_at, CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_user ADD CONSTRAINT FK_C4EA01CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
    }
}
