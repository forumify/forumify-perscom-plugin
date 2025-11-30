<?php

declare(strict_types=1);

namespace ForumifyPerscomPluginMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251130172044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '1.0 migrations update';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_BA443E508B8E8428 ON perscom_after_action_report (created_at)');
        $this->addSql('CREATE INDEX IDX_BA443E5043625D9F ON perscom_after_action_report (updated_at)');
        $this->addSql('CREATE INDEX IDX_89A4B53C462CE4F5 ON perscom_award (position)');
        $this->addSql('CREATE INDEX IDX_89A4B53C8B8E8428 ON perscom_award (created_at)');
        $this->addSql('CREATE INDEX IDX_89A4B53C43625D9F ON perscom_award (updated_at)');
        $this->addSql('ALTER TABLE perscom_course CHANGE prerequisites prerequisites LONGTEXT DEFAULT NULL, CHANGE qualifications qualifications LONGTEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_79C9B7E462CE4F5 ON perscom_course (position)');
        $this->addSql('CREATE INDEX IDX_254C72A8B8E8428 ON perscom_course_class (created_at)');
        $this->addSql('CREATE INDEX IDX_254C72A43625D9F ON perscom_course_class (updated_at)');
        $this->addSql('ALTER TABLE perscom_course_class_student CHANGE qualifications qualifications LONGTEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_E1CC017D462CE4F5 ON perscom_course_instructor (position)');
        $this->addSql('CREATE INDEX IDX_E602ECF88B8E8428 ON perscom_document (created_at)');
        $this->addSql('CREATE INDEX IDX_E602ECF843625D9F ON perscom_document (updated_at)');
        $this->addSql('CREATE INDEX IDX_1BF12ACD8B8E8428 ON perscom_form (created_at)');
        $this->addSql('CREATE INDEX IDX_1BF12ACD43625D9F ON perscom_form (updated_at)');
        $this->addSql('CREATE INDEX IDX_D963AE58462CE4F5 ON perscom_form_field (position)');
        $this->addSql('CREATE INDEX IDX_D963AE588B8E8428 ON perscom_form_field (created_at)');
        $this->addSql('CREATE INDEX IDX_D963AE5843625D9F ON perscom_form_field (updated_at)');
        $this->addSql('ALTER TABLE perscom_form_field RENAME INDEX uniq_d963ae585ff69b7d4e645a7e TO UNIQ_D963AE585FF69B7DF48571EB');
        $this->addSql('CREATE INDEX IDX_2A50A2418B8E8428 ON perscom_form_submission (created_at)');
        $this->addSql('CREATE INDEX IDX_2A50A24143625D9F ON perscom_form_submission (updated_at)');
        $this->addSql('CREATE INDEX IDX_9576A7DB8B8E8428 ON perscom_mission (created_at)');
        $this->addSql('CREATE INDEX IDX_9576A7DB43625D9F ON perscom_mission (updated_at)');
        $this->addSql('CREATE INDEX IDX_7847827B462CE4F5 ON perscom_position (position)');
        $this->addSql('CREATE INDEX IDX_7847827B8B8E8428 ON perscom_position (created_at)');
        $this->addSql('CREATE INDEX IDX_7847827B43625D9F ON perscom_position (updated_at)');
        $this->addSql('CREATE INDEX IDX_A2CE695E462CE4F5 ON perscom_qualification (position)');
        $this->addSql('CREATE INDEX IDX_A2CE695E8B8E8428 ON perscom_qualification (created_at)');
        $this->addSql('CREATE INDEX IDX_A2CE695E43625D9F ON perscom_qualification (updated_at)');
        $this->addSql('CREATE INDEX IDX_C1003F67462CE4F5 ON perscom_rank (position)');
        $this->addSql('CREATE INDEX IDX_C1003F678B8E8428 ON perscom_rank (created_at)');
        $this->addSql('CREATE INDEX IDX_C1003F6743625D9F ON perscom_rank (updated_at)');
        $this->addSql('CREATE INDEX IDX_75527C688B8E8428 ON perscom_record_assignment (created_at)');
        $this->addSql('CREATE INDEX IDX_75527C6843625D9F ON perscom_record_assignment (updated_at)');
        $this->addSql('CREATE INDEX IDX_DB456E68B8E8428 ON perscom_record_award (created_at)');
        $this->addSql('CREATE INDEX IDX_DB456E643625D9F ON perscom_record_award (updated_at)');
        $this->addSql('CREATE INDEX IDX_FAD13C768B8E8428 ON perscom_record_combat (created_at)');
        $this->addSql('CREATE INDEX IDX_FAD13C7643625D9F ON perscom_record_combat (updated_at)');
        $this->addSql('CREATE INDEX IDX_40EBABC98B8E8428 ON perscom_record_qualification (created_at)');
        $this->addSql('CREATE INDEX IDX_40EBABC943625D9F ON perscom_record_qualification (updated_at)');
        $this->addSql('CREATE INDEX IDX_6FA23B748B8E8428 ON perscom_record_rank (created_at)');
        $this->addSql('CREATE INDEX IDX_6FA23B7443625D9F ON perscom_record_rank (updated_at)');
        $this->addSql('CREATE INDEX IDX_A658D5728B8E8428 ON perscom_record_service (created_at)');
        $this->addSql('CREATE INDEX IDX_A658D57243625D9F ON perscom_record_service (updated_at)');
        $this->addSql('CREATE INDEX IDX_71BB593E462CE4F5 ON perscom_roster (position)');
        $this->addSql('CREATE INDEX IDX_71BB593E8B8E8428 ON perscom_roster (created_at)');
        $this->addSql('CREATE INDEX IDX_71BB593E43625D9F ON perscom_roster (updated_at)');
        $this->addSql('CREATE INDEX IDX_EA5863AD462CE4F5 ON perscom_specialty (position)');
        $this->addSql('CREATE INDEX IDX_EA5863AD8B8E8428 ON perscom_specialty (created_at)');
        $this->addSql('CREATE INDEX IDX_EA5863AD43625D9F ON perscom_specialty (updated_at)');
        $this->addSql('CREATE INDEX IDX_6A0291DB462CE4F5 ON perscom_status (position)');
        $this->addSql('CREATE INDEX IDX_6A0291DB8B8E8428 ON perscom_status (created_at)');
        $this->addSql('CREATE INDEX IDX_6A0291DB43625D9F ON perscom_status (updated_at)');
        $this->addSql('ALTER TABLE perscom_sync_result CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_95C2DBD1462CE4F5 ON perscom_unit (position)');
        $this->addSql('CREATE INDEX IDX_95C2DBD18B8E8428 ON perscom_unit (created_at)');
        $this->addSql('CREATE INDEX IDX_95C2DBD143625D9F ON perscom_unit (updated_at)');
        $this->addSql('CREATE INDEX IDX_C4EA01CB8B8E8428 ON perscom_user (created_at)');
        $this->addSql('CREATE INDEX IDX_C4EA01CB43625D9F ON perscom_user (updated_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_BA443E508B8E8428 ON perscom_after_action_report');
        $this->addSql('DROP INDEX IDX_BA443E5043625D9F ON perscom_after_action_report');
        $this->addSql('DROP INDEX IDX_89A4B53C462CE4F5 ON perscom_award');
        $this->addSql('DROP INDEX IDX_89A4B53C8B8E8428 ON perscom_award');
        $this->addSql('DROP INDEX IDX_89A4B53C43625D9F ON perscom_award');
        $this->addSql('DROP INDEX IDX_79C9B7E462CE4F5 ON perscom_course');
        $this->addSql('ALTER TABLE perscom_course CHANGE prerequisites prerequisites LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE qualifications qualifications LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('DROP INDEX IDX_254C72A8B8E8428 ON perscom_course_class');
        $this->addSql('DROP INDEX IDX_254C72A43625D9F ON perscom_course_class');
        $this->addSql('ALTER TABLE perscom_course_class_student CHANGE qualifications qualifications LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('DROP INDEX IDX_E1CC017D462CE4F5 ON perscom_course_instructor');
        $this->addSql('DROP INDEX IDX_E602ECF88B8E8428 ON perscom_document');
        $this->addSql('DROP INDEX IDX_E602ECF843625D9F ON perscom_document');
        $this->addSql('DROP INDEX IDX_1BF12ACD8B8E8428 ON perscom_form');
        $this->addSql('DROP INDEX IDX_1BF12ACD43625D9F ON perscom_form');
        $this->addSql('DROP INDEX IDX_D963AE58462CE4F5 ON perscom_form_field');
        $this->addSql('DROP INDEX IDX_D963AE588B8E8428 ON perscom_form_field');
        $this->addSql('DROP INDEX IDX_D963AE5843625D9F ON perscom_form_field');
        $this->addSql('ALTER TABLE perscom_form_field RENAME INDEX uniq_d963ae585ff69b7df48571eb TO UNIQ_D963AE585FF69B7D4E645A7E');
        $this->addSql('DROP INDEX IDX_2A50A2418B8E8428 ON perscom_form_submission');
        $this->addSql('DROP INDEX IDX_2A50A24143625D9F ON perscom_form_submission');
        $this->addSql('DROP INDEX IDX_9576A7DB8B8E8428 ON perscom_mission');
        $this->addSql('DROP INDEX IDX_9576A7DB43625D9F ON perscom_mission');
        $this->addSql('DROP INDEX IDX_7847827B462CE4F5 ON perscom_position');
        $this->addSql('DROP INDEX IDX_7847827B8B8E8428 ON perscom_position');
        $this->addSql('DROP INDEX IDX_7847827B43625D9F ON perscom_position');
        $this->addSql('DROP INDEX IDX_A2CE695E462CE4F5 ON perscom_qualification');
        $this->addSql('DROP INDEX IDX_A2CE695E8B8E8428 ON perscom_qualification');
        $this->addSql('DROP INDEX IDX_A2CE695E43625D9F ON perscom_qualification');
        $this->addSql('DROP INDEX IDX_C1003F67462CE4F5 ON perscom_rank');
        $this->addSql('DROP INDEX IDX_C1003F678B8E8428 ON perscom_rank');
        $this->addSql('DROP INDEX IDX_C1003F6743625D9F ON perscom_rank');
        $this->addSql('DROP INDEX IDX_75527C688B8E8428 ON perscom_record_assignment');
        $this->addSql('DROP INDEX IDX_75527C6843625D9F ON perscom_record_assignment');
        $this->addSql('DROP INDEX IDX_DB456E68B8E8428 ON perscom_record_award');
        $this->addSql('DROP INDEX IDX_DB456E643625D9F ON perscom_record_award');
        $this->addSql('DROP INDEX IDX_FAD13C768B8E8428 ON perscom_record_combat');
        $this->addSql('DROP INDEX IDX_FAD13C7643625D9F ON perscom_record_combat');
        $this->addSql('DROP INDEX IDX_40EBABC98B8E8428 ON perscom_record_qualification');
        $this->addSql('DROP INDEX IDX_40EBABC943625D9F ON perscom_record_qualification');
        $this->addSql('DROP INDEX IDX_6FA23B748B8E8428 ON perscom_record_rank');
        $this->addSql('DROP INDEX IDX_6FA23B7443625D9F ON perscom_record_rank');
        $this->addSql('DROP INDEX IDX_A658D5728B8E8428 ON perscom_record_service');
        $this->addSql('DROP INDEX IDX_A658D57243625D9F ON perscom_record_service');
        $this->addSql('DROP INDEX IDX_71BB593E462CE4F5 ON perscom_roster');
        $this->addSql('DROP INDEX IDX_71BB593E8B8E8428 ON perscom_roster');
        $this->addSql('DROP INDEX IDX_71BB593E43625D9F ON perscom_roster');
        $this->addSql('DROP INDEX IDX_EA5863AD462CE4F5 ON perscom_specialty');
        $this->addSql('DROP INDEX IDX_EA5863AD8B8E8428 ON perscom_specialty');
        $this->addSql('DROP INDEX IDX_EA5863AD43625D9F ON perscom_specialty');
        $this->addSql('DROP INDEX IDX_6A0291DB462CE4F5 ON perscom_status');
        $this->addSql('DROP INDEX IDX_6A0291DB8B8E8428 ON perscom_status');
        $this->addSql('DROP INDEX IDX_6A0291DB43625D9F ON perscom_status');
        $this->addSql('ALTER TABLE perscom_sync_result CHANGE start start DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE end end DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP INDEX IDX_95C2DBD1462CE4F5 ON perscom_unit');
        $this->addSql('DROP INDEX IDX_95C2DBD18B8E8428 ON perscom_unit');
        $this->addSql('DROP INDEX IDX_95C2DBD143625D9F ON perscom_unit');
        $this->addSql('DROP INDEX IDX_C4EA01CB8B8E8428 ON perscom_user');
        $this->addSql('DROP INDEX IDX_C4EA01CB43625D9F ON perscom_user');
    }
}
