<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241207132633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add courses';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE perscom_course (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, rank_requirement INT DEFAULT NULL, prerequisites LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', qualifications LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', slug VARCHAR(255) NOT NULL, position INT NOT NULL, UNIQUE INDEX UNIQ_79C9B7E989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE perscom_course_class (id INT AUTO_INCREMENT NOT NULL, course_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, signup_from DATETIME NOT NULL, signup_until DATETIME NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, instructors LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', instructor_slots INT DEFAULT NULL, students LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', student_slots INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_254C72A591CC992 (course_id), INDEX IDX_254C72ADE12AB56 (created_by), INDEX IDX_254C72A16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE perscom_course_class_result (id INT AUTO_INCREMENT NOT NULL, class_id INT DEFAULT NULL, result JSON NOT NULL, UNIQUE INDEX UNIQ_EAE3CFCEEA000B10 (class_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE perscom_course_class ADD CONSTRAINT FK_254C72A591CC992 FOREIGN KEY (course_id) REFERENCES perscom_course (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perscom_course_class ADD CONSTRAINT FK_254C72ADE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE perscom_course_class ADD CONSTRAINT FK_254C72A16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE perscom_course_class_result ADD CONSTRAINT FK_EAE3CFCEEA000B10 FOREIGN KEY (class_id) REFERENCES perscom_course_class (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_course_class DROP FOREIGN KEY FK_254C72A591CC992');
        $this->addSql('ALTER TABLE perscom_course_class DROP FOREIGN KEY FK_254C72ADE12AB56');
        $this->addSql('ALTER TABLE perscom_course_class DROP FOREIGN KEY FK_254C72A16FE72E1');
        $this->addSql('ALTER TABLE perscom_course_class_result DROP FOREIGN KEY FK_EAE3CFCEEA000B10');
        $this->addSql('DROP TABLE perscom_course');
        $this->addSql('DROP TABLE perscom_course_class');
        $this->addSql('DROP TABLE perscom_course_class_result');
    }
}
