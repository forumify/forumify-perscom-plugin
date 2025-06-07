<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250601095046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add instructor types to courses';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_course_instructor (id INT AUTO_INCREMENT NOT NULL, course_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, position INT NOT NULL, INDEX IDX_E1CC017D591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_instructor ADD CONSTRAINT FK_E1CC017D591CC992 FOREIGN KEY (course_id) REFERENCES perscom_course (id) ON DELETE CASCADE
        SQL);

        $courses = $this->connection->executeQuery('SELECT id FROM perscom_course')->fetchFirstColumn();
        foreach ($courses as $courseId) {
            $this->addSql('INSERT INTO perscom_course_instructor (course_id, title, description, position) VALUES (?, "Instructor", "", 1)', [$courseId]);
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_instructor DROP FOREIGN KEY FK_E1CC017D591CC992
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_course_instructor
        SQL);
    }
}
