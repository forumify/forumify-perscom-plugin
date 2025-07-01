<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\Core\Form\RichTextEditorType;
use Forumify\Core\Repository\RoleRepository;
use Forumify\Forum\Repository\ForumRepository;
use Forumify\PerscomPlugin\Perscom\Form\PerscomFormType;
use Forumify\PerscomPlugin\Perscom\Form\StatusType;
use Forumify\Plugin\Service\PluginVersionChecker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ConfigurationType extends AbstractType
{
    public function __construct(
        private readonly ForumRepository $forumRepository,
        private readonly RoleRepository $roleRepository,
        private readonly PluginVersionChecker $pluginVersionChecker,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('perscom__opcenter__announcement', RichTextEditorType::class, [
                'help' => 'Content to show on the operations center.',
                'label' => 'Announcement',
                'required' => false,
            ])
            // Enlistment
            ->add('perscom__enlistment__status', StatusType::class, [
                'autocomplete' => true,
                'help' => 'Only users in these statuses can start the enlistment process. Users who do not have a PERSCOM account yet will always be allowed to enlist.',
                'label' => 'Eligible Status',
                'multiple' => true,
                'required' => false,
            ])
            ->add('perscom__enlistment__form', PerscomFormType::class, [
                'help' => 'The form to use for enlistments, by default, all required fields to create a PERSCOM user are already added by this plugin.',
                'label' => 'Enlistment Form',
                'placeholder' => 'Select a form to use for enlistments',
                'required' => false,
            ])
            ->add('perscom__enlistment__forum', ChoiceType::class, [
                'choices' => $this->getForumChoices(),
                'help' => 'Automatically post a topic containing the enlistment to this forum.',
                'label' => 'Enlistment Forum',
                'placeholder' => 'Do not create enlistment topics',
                'required' => false,
            ])
            ->add('perscom__enlistment__role', ChoiceType::class, [
                'choices' => $this->getRoleChoices(),
                'help' => 'Automatically assign this role to the user upon creating an enlistment.',
                'label' => 'Enlistee Role',
                'placeholder' => 'Do not assign a role',
                'required' => false,
            ])
            // Profiles
            ->add('perscom__profile__overwrite_display_names', CheckboxType::class, [
                'help' => 'Automatically set a user\'s forum display name to match their PERSCOM name.',
                'label' => 'Overwrite user display names',
                'required' => false,
            ])
            ->add('perscom__profile__display_name_format', TextType::class, [
                'empty_data' => '{user.rank.abbreviation} {user.name}',
                'help' => 'perscom.settings.profile.display_name_format_help',
                'help_html' => true,
                'label' => 'Display name format',
                'required' => false,
            ])
            ->add('perscom__profile__overwrite_avatars', CheckboxType::class, [
                'help' => 'Automatically set the user\'s avatar to match their PERSCOM rank.',
                'label' => 'Overwrite user avatar',
                'required' => false,
            ])
            ->add('perscom__profile__overwrite_signatures', CheckboxType::class, [
                'help' => 'Automatically set the user\'s signature to match their PERSCOM signature.',
                'label' => 'Overwrite user signature',
                'required' => false,
            ])
        ;

        if ($this->pluginVersionChecker->isVersionInstalled('forumify/forumify-perscom-plugin', 'premium')) {
            $builder
                // Reporting In
                ->add('perscom__report_in__enabled', CheckboxType::class, [
                    'help' => 'Enforces users to press the "Report In" button on the operations center at least once every x days',
                    'label' => 'Enabled',
                    'required' => false,
                ])
                ->add('perscom__report_in__enabled_status', StatusType::class, [
                    'autocomplete' => true,
                    'help' => 'Which statuses need to be checked for report in activity?',
                    'label' => 'Enabled status',
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('perscom__report_in__period', NumberType::class, [
                    'help' => 'If a user hasn\'t reported in during this time, their status will be changed.',
                    'html5' => true,
                    'label' => 'Period (in days)',
                    'required' => false,
                    'scale' => 0,
                ])
                ->add('perscom__report_in__warning_period', NumberType::class, [
                    'help' => 'If a user hasn\'t reported in during this time, a warning notification will be sent. Leave blank to disable warnings.',
                    'html5' => true,
                    'label' => 'Warning Period (in days)',
                    'required' => false,
                    'scale' => 0,
                ])
                ->add('perscom__report_in__failure_status', StatusType::class, [
                    'help' => 'Status to move the user to when they fail to report in. For example: AWOL',
                    'label' => 'Failure status',
                    'required' => false,
                ])
                // Operations
                ->add('perscom__operations__absent_notification', CheckboxType::class, [
                    'help' => 'Send a simple notification when the user is marked absent in an after action report.',
                    'label' => 'Send absent notifications',
                    'required' => false,
                ])
                ->add('perscom__operations__absent_notification_message', TextType::class, [
                    'constraints' => [new Assert\Length(max: 300)],
                    'help' => 'If absent notifications are turned on, and this field is empty, a standard message will be used. Max 300 characters.',
                    'label' => 'Absent notification message',
                    'required' => false,
                ])
                ->add('perscom__operations__consecutive_absent_notification', CheckboxType::class, [
                    'help' => 'Send an email and optionally change the user\'s status when they are marked absent multiple times in a row.',
                    'label' => 'Send consecutive absence notifications',
                    'required' => false,
                ])
                ->add('perscom__operations__consecutive_absent_notification_count', NumberType::class, [
                    'constraints' => [new Assert\PositiveOrZero()],
                    'help' => 'How many times the user needs to be marked absent in an after action report before being considered consecutively absent. For example, if set to 3, the user will receive their first notification on their third absence.',
                    'label' => 'Consecutive absence count',
                    'required' => false,
                ])
                ->add('perscom__operations__consecutive_absent_notification_message', RichTextEditorType::class, [
                    'help' => 'If this is empty, a standard message will be used.',
                    'label' => 'Consecutive absence email content',
                    'required' => false,
                ])
                ->add('perscom__operations__consecutive_absent_status', StatusType::class, [
                    'help' => 'Automatically move users with consecutive absences to a different status.',
                    'label' => 'Consecutive absence status',
                    'placeholder' => 'Do not change status',
                    'required' => false,
                ])
            ;
        }
    }

    private function getForumChoices(): array
    {
        $choices = $this->forumRepository
            ->createQueryBuilder('f')
            ->select('f.id', 'f.title')
            ->getQuery()
            ->getArrayResult()
        ;

        return array_combine(
            array_column($choices, 'title'),
            array_column($choices, 'id'),
        );
    }

    private function getRoleChoices(): array
    {
        $choices = $this->roleRepository
            ->createQueryBuilder('r')
            ->select('r.id', 'r.title')
            ->getQuery()
            ->getArrayResult()
        ;

        return array_combine(
            array_column($choices, 'title'),
            array_column($choices, 'id'),
        );
    }
}
