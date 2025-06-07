<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250601131117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'move json mess into dedicated entities';
    }

    public function up(Schema $schema): void
    {
        $classes = $this->connection->executeQuery('SELECT id, course_id, instructors, students FROM perscom_course_class')->fetchAllAssociative();
        $allResults = $this->connection->executeQuery('SELECT class_id, result FROM perscom_course_class_result')->fetchAllAssociative();
        $results = [];
        foreach ($allResults as $result) {
            $results[$result['class_id']] = json_decode($result['result'], true);
        }

        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_course_class_instructor (perscom_user_id INT NOT NULL, class_id INT NOT NULL, instructor_id INT DEFAULT NULL, present TINYINT(1) DEFAULT NULL, INDEX IDX_2B5CA9FEA000B10 (class_id), INDEX IDX_2B5CA9F8C4FC193 (instructor_id), PRIMARY KEY(perscom_user_id, class_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE perscom_course_class_student (perscom_user_id INT NOT NULL, class_id INT NOT NULL, result VARCHAR(255) DEFAULT NULL, qualifications LONGTEXT DEFAULT NULL COMMENT '(DC2Type:simple_array)', service_record_text_override VARCHAR(255) DEFAULT NULL, INDEX IDX_B1DB8C16EA000B10 (class_id), PRIMARY KEY(perscom_user_id, class_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_class_instructor ADD CONSTRAINT FK_2B5CA9FEA000B10 FOREIGN KEY (class_id) REFERENCES perscom_course_class (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_class_instructor ADD CONSTRAINT FK_2B5CA9F8C4FC193 FOREIGN KEY (instructor_id) REFERENCES perscom_course_instructor (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_class_student ADD CONSTRAINT FK_B1DB8C16EA000B10 FOREIGN KEY (class_id) REFERENCES perscom_course_class (id) ON DELETE CASCADE
        SQL);

        foreach ($classes as $class) {
            $result = $results[$class['id']] ?? null;

            $instructors = empty($class['instructors']) ? [] : explode(',', $class['instructors']);
            foreach ($instructors as $instructor) {
                $this->addSql('INSERT INTO perscom_course_class_instructor (perscom_user_id, class_id, present) VALUES (?, ?, ?, ?)', [
                    $instructor,
                    $class['id'],
                    $result['instructors'][$instructor]['attended'] ?? null,
                ]);
            }

            $students = empty($class['students']) ? [] : explode(',', $class['students']);
            foreach ($students as $student) {
                $sResult = $result['students'][$student] ?? null;
                if ($sResult !== null) {
                    $this->addSql('INSERT INTO perscom_course_class_student (perscom_user_id, class_id, result, qualifications, service_record_text_override) VALUES (?, ?, ?, ?, ?)', [
                        $student,
                        $class['id'],
                        $sResult['result'] ?? null,
                        empty($sResult['qualifications']) ? null : implode(',', $sResult['qualifications']),
                        $sResult['service_record_text'] ?? null,
                    ]);
                } else {
                    $this->addSql('INSERT INTO perscom_course_class_student (perscom_user_id, class_id) VALUES (?, ?)', [
                        $student,
                        $class['id'],
                    ]);
                }
            }
        }

        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_class DROP instructors, DROP students, DROP instructor_slots
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_class_result DROP FOREIGN KEY FK_EAE3CFCEEA000B10
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_course_class_result
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_class ADD result TINYINT(1) DEFAULT 0 NOT NULL, DROP instructors, DROP students;
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_class_instructor DROP FOREIGN KEY FK_2B5CA9FEA000B10
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_class_instructor DROP FOREIGN KEY FK_2B5CA9F8C4FC193
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_class_student DROP FOREIGN KEY FK_B1DB8C16EA000B10
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_course_class_instructor
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE perscom_course_class_student
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE perscom_course_class ADD instructors LONGTEXT DEFAULT NULL COMMENT '(DC2Type:simple_array)', ADD students LONGTEXT DEFAULT NULL COMMENT '(DC2Type:simple_array)', ADD instructor_slots INT DEFAULT NULL
        SQL);
    }
}
