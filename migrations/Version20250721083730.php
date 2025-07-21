<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250721083730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'remove perscom ids as foreign keys';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE perscom_mission_rsvp ADD id INT AUTO_INCREMENT NOT NULL FIRST, ADD user_id INT DEFAULT NULL AFTER id, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE perscom_mission_rsvp ADD CONSTRAINT FK_B053BB1CA76ED395 FOREIGN KEY (user_id) REFERENCES perscom_user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B053BB1CA76ED395 ON perscom_mission_rsvp (user_id)');
        $this->addSql('DROP INDEX unit_mission_uniq ON perscom_after_action_report');
        $this->addSql('ALTER TABLE perscom_after_action_report CHANGE unit_id perscom_unit_id INT NOT NULL, DROP unit_name, DROP unit_position, CHANGE mission_id mission_id INT NOT NULL, ADD unit_id INT DEFAULT NULL AFTER mission_id');
        $this->addSql('ALTER TABLE perscom_after_action_report ADD CONSTRAINT FK_BA443E50F8BD700D FOREIGN KEY (unit_id) REFERENCES perscom_unit (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_BA443E50F8BD700D ON perscom_after_action_report (unit_id)');
        $this->addSql('ALTER TABLE perscom_course_class_instructor ADD id INT AUTO_INCREMENT NOT NULL FIRST, ADD user_id INT DEFAULT NULL AFTER id, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE perscom_course_class_instructor ADD CONSTRAINT FK_C17C6629A76ED395 FOREIGN KEY (user_id) REFERENCES perscom_user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_C17C6629A76ED395 ON perscom_course_class_instructor (user_id)');
        $this->addSql('ALTER TABLE perscom_course_class_student ADD id INT AUTO_INCREMENT NOT NULL FIRST, ADD user_id INT DEFAULT NULL AFTER id, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE perscom_course_class_student ADD CONSTRAINT FK_4FB88854A76ED395 FOREIGN KEY (user_id) REFERENCES perscom_user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4FB88854A76ED395 ON perscom_course_class_student (user_id)');
        $this->addSql('ALTER TABLE perscom_enlistment_topic DROP FOREIGN KEY FK_A96B73621F55203D');
        $this->addSql('ALTER TABLE perscom_enlistment_topic DROP FOREIGN KEY FK_A96B7362A76ED395');
        $this->addSql('DROP TABLE perscom_enlistment_topic');
        $this->addSql('ALTER TABLE perscom_report_in ADD id INT AUTO_INCREMENT NOT NULL FIRST, ADD user_id INT DEFAULT NULL AFTER id, ADD return_status_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE perscom_report_in ADD CONSTRAINT FK_A139889FA76ED395 FOREIGN KEY (user_id) REFERENCES perscom_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perscom_report_in ADD CONSTRAINT FK_A139889F9EEEBE0D FOREIGN KEY (return_status_id) REFERENCES perscom_status (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_A139889FA76ED395 ON perscom_report_in (user_id)');
        $this->addSql('CREATE INDEX IDX_A139889F9EEEBE0D ON perscom_report_in (return_status_id)');
        $this->addSql('ALTER TABLE perscom_user ADD enlistment_topic_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_user ADD CONSTRAINT FK_C4EA01CB447F7CAA FOREIGN KEY (enlistment_topic_id) REFERENCES topic (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4EA01CB447F7CAA ON perscom_user (enlistment_topic_id)');
        $this->addSql('ALTER TABLE perscom_course ADD minimum_rank_id INT DEFAULT NULL AFTER rank_requirement');
        $this->addSql('ALTER TABLE perscom_course ADD CONSTRAINT FK_79C9B7E4BF66E9D FOREIGN KEY (minimum_rank_id) REFERENCES perscom_rank (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_79C9B7E4BF66E9D ON perscom_course (minimum_rank_id)');
        $this->addSql('ALTER TABLE perscom_after_action_report CHANGE perscom_unit_id perscom_unit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_course_class_instructor CHANGE perscom_user_id perscom_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_course_class_instructor RENAME INDEX idx_2b5ca9fea000b10 TO IDX_C17C6629EA000B10');
        $this->addSql('ALTER TABLE perscom_course_class_instructor RENAME INDEX idx_2b5ca9f8c4fc193 TO IDX_C17C66298C4FC193');
        $this->addSql('ALTER TABLE perscom_course_class_student CHANGE perscom_user_id perscom_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_course_class_student RENAME INDEX idx_b1db8c16ea000b10 TO IDX_4FB88854EA000B10');
        $this->addSql('ALTER TABLE perscom_report_in CHANGE perscom_user_id perscom_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perscom_user_secondary_positions DROP FOREIGN KEY FK_41B91062A27F8672');
        $this->addSql('ALTER TABLE perscom_user_secondary_positions DROP FOREIGN KEY FK_41B91062DD842E46');
        $this->addSql('DROP TABLE perscom_user_secondary_positions');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE perscom_user_secondary_positions (perscom_user_id INT NOT NULL, position_id INT NOT NULL, INDEX IDX_41B91062A27F8672 (perscom_user_id), INDEX IDX_41B91062DD842E46 (position_id), PRIMARY KEY(perscom_user_id, position_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE perscom_user_secondary_positions ADD CONSTRAINT FK_41B91062A27F8672 FOREIGN KEY (perscom_user_id) REFERENCES perscom_user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perscom_user_secondary_positions ADD CONSTRAINT FK_41B91062DD842E46 FOREIGN KEY (position_id) REFERENCES perscom_position (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perscom_after_action_report CHANGE perscom_unit_id perscom_unit_id INT NOT NULL');
        $this->addSql('ALTER TABLE perscom_course_class_instructor CHANGE perscom_user_id perscom_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE perscom_course_class_instructor RENAME INDEX idx_c17c66298c4fc193 TO IDX_2B5CA9F8C4FC193');
        $this->addSql('ALTER TABLE perscom_course_class_instructor RENAME INDEX idx_c17c6629ea000b10 TO IDX_2B5CA9FEA000B10');
        $this->addSql('ALTER TABLE perscom_course_class_student CHANGE perscom_user_id perscom_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE perscom_course_class_student RENAME INDEX idx_4fb88854ea000b10 TO IDX_B1DB8C16EA000B10');
        $this->addSql('ALTER TABLE perscom_report_in CHANGE perscom_user_id perscom_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE perscom_course DROP FOREIGN KEY FK_79C9B7E4BF66E9D');
        $this->addSql('DROP INDEX IDX_79C9B7E4BF66E9D ON perscom_course');
        $this->addSql('ALTER TABLE perscom_course DROP minimum_rank_id');
        $this->addSql('CREATE TABLE perscom_enlistment_topic (submission_id INT NOT NULL, user_id INT DEFAULT NULL, topic_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A96B73621F55203D (topic_id), INDEX IDX_A96B7362A76ED395 (user_id), PRIMARY KEY(submission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE perscom_enlistment_topic ADD CONSTRAINT FK_A96B73621F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perscom_enlistment_topic ADD CONSTRAINT FK_A96B7362A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perscom_report_in MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE perscom_report_in DROP FOREIGN KEY FK_A139889FA76ED395');
        $this->addSql('ALTER TABLE perscom_report_in DROP FOREIGN KEY FK_A139889F9EEEBE0D');
        $this->addSql('DROP INDEX IDX_A139889FA76ED395 ON perscom_report_in');
        $this->addSql('DROP INDEX IDX_A139889F9EEEBE0D ON perscom_report_in');
        $this->addSql('DROP INDEX `PRIMARY` ON perscom_report_in');
        $this->addSql('ALTER TABLE perscom_report_in DROP id, DROP user_id, DROP return_status_id');
        $this->addSql('ALTER TABLE perscom_report_in ADD PRIMARY KEY (perscom_user_id)');
        $this->addSql('ALTER TABLE perscom_user DROP FOREIGN KEY FK_C4EA01CB447F7CAA');
        $this->addSql('DROP INDEX UNIQ_C4EA01CB447F7CAA ON perscom_user');
        $this->addSql('ALTER TABLE perscom_user DROP enlistment_topic_id');
        $this->addSql('ALTER TABLE perscom_course_class_student MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE perscom_course_class_student DROP FOREIGN KEY FK_4FB88854A76ED395');
        $this->addSql('DROP INDEX IDX_4FB88854A76ED395 ON perscom_course_class_student');
        $this->addSql('DROP INDEX `PRIMARY` ON perscom_course_class_student');
        $this->addSql('ALTER TABLE perscom_course_class_student DROP id, DROP user_id');
        $this->addSql('ALTER TABLE perscom_course_class_student ADD PRIMARY KEY (perscom_user_id, class_id)');
        $this->addSql('ALTER TABLE perscom_course_class_instructor MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE perscom_course_class_instructor DROP FOREIGN KEY FK_C17C6629A76ED395');
        $this->addSql('DROP INDEX IDX_C17C6629A76ED395 ON perscom_course_class_instructor');
        $this->addSql('DROP INDEX `PRIMARY` ON perscom_course_class_instructor');
        $this->addSql('ALTER TABLE perscom_course_class_instructor DROP id, DROP user_id');
        $this->addSql('ALTER TABLE perscom_course_class_instructor ADD PRIMARY KEY (perscom_user_id, class_id)');
        $this->addSql('ALTER TABLE perscom_after_action_report DROP FOREIGN KEY FK_BA443E50F8BD700D');
        $this->addSql('DROP INDEX IDX_BA443E50F8BD700D ON perscom_after_action_report');
        $this->addSql('ALTER TABLE perscom_after_action_report ADD unit_name VARCHAR(255) NOT NULL, ADD unit_position INT DEFAULT 100 NOT NULL, CHANGE perscom_unit_id unit_id INT NOT NULL, CHANGE mission_id mission_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX unit_mission_uniq ON perscom_after_action_report (mission_id, unit_id)');
        $this->addSql('ALTER TABLE perscom_mission_rsvp MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE perscom_mission_rsvp DROP FOREIGN KEY FK_B053BB1CA76ED395');
        $this->addSql('DROP INDEX IDX_B053BB1CA76ED395 ON perscom_mission_rsvp');
        $this->addSql('DROP INDEX `PRIMARY` ON perscom_mission_rsvp');
        $this->addSql('ALTER TABLE perscom_mission_rsvp DROP id, DROP user_id');
        $this->addSql('ALTER TABLE perscom_mission_rsvp ADD PRIMARY KEY (perscom_user_id, mission_id)');
    }
}
