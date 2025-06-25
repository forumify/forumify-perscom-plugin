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
                'label' => 'Announcement',
                'help' => 'Content to show on the operations center.',
                'required' => false,
            ])
            // Enlistment
            ->add('perscom__enlistment__status', StatusType::class, [
                'label' => 'Eligible Status',
                'help' => 'Only users in these statuses can start the enlistment process. Users who do not have a PERSCOM account yet will always be allowed to enlist.',
                'multiple' => true,
                'required' => false,
                'autocomplete' => true,
            ])
            ->add('perscom__enlistment__form', PerscomFormType::class, [
                'label' => 'Enlistment Form',
                'required' => false,
                'help' => 'The form to use for enlistments, by default, all required fields to create a PERSCOM user are already added by this plugin.',
                'placeholder' => 'Select a form to use for enlistments',
            ])
            ->add('perscom__enlistment__forum', ChoiceType::class, [
                'label' => 'Enlistment Forum',
                'help' => 'Automatically post a topic containing the enlistment to this forum.',
                'required' => false,
                'choices' => $this->getForumChoices(),
                'placeholder' => 'Do not create enlistment topics',
            ])
            ->add('perscom__enlistment__role', ChoiceType::class, [
                'label' => 'Enlistee Role',
                'help' => 'Automatically assign this role to the user upon creating an enlistment.',
                'required' => false,
                'choices' => $this->getRoleChoices(),
                'placeholder' => 'Do not assign a role',
            ])
            // Profiles
            ->add('perscom__profile__overwrite_display_names', CheckboxType::class, [
                'label' => 'Overwrite user display names',
                'help' => 'Automatically set a user\'s forum display name to match their PERSCOM name.',
                'required' => false,
            ])
            ->add('perscom__profile__display_name_format', TextType::class, [
                'label' => 'Display name format',
                'help' => 'perscom.settings.profile.display_name_format_help',
                'help_html' => true,
                'required' => false,
                'empty_data' => '{user.rank.abbreviation} {user.name}',
            ])
            ->add('perscom__profile__overwrite_avatars', CheckboxType::class, [
                'label' => 'Overwrite user avatar',
                'help' => 'Automatically set the user\'s avatar to match their PERSCOM rank.',
                'required' => false,
            ])
            ->add('perscom__profile__overwrite_signatures', CheckboxType::class, [
                'label' => 'Overwrite user signature',
                'help' => 'Automatically set the user\'s signature to match their PERSCOM signature.',
                'required' => false,
            ])
        ;

        if ($this->pluginVersionChecker->isVersionInstalled('forumify/forumify-perscom-plugin', 'premium')) {
            $builder
                // Reporting In
                ->add('perscom__report_in__enabled', CheckboxType::class, [
                    'label' => 'Enabled',
                    'help' => 'Enforces users to press the "Report In" button on the operations center at least once every x days',
                    'required' => false,
                ])
                ->add('perscom__report_in__enabled_status', StatusType::class, [
                    'label' => 'Enabled status',
                    'help' => 'Which statuses need to be checked for report in activity?',
                    'required' => false,
                    'multiple' => true,
                    'autocomplete' => true,
                ])
                ->add('perscom__report_in__period', NumberType::class, [
                    'label' => 'Period (in days)',
                    'help' => 'If a user hasn\'t reported in during this time, their status will be changed.',
                    'required' => false,
                    'scale' => 0,
                    'html5' => true,
                ])
                ->add('perscom__report_in__warning_period', NumberType::class, [
                    'label' => 'Warning Period (in days)',
                    'help' => 'If a user hasn\'t reported in during this time, a warning notification will be sent. Leave blank to disable warnings.',
                    'required' => false,
                    'scale' => 0,
                    'html5' => true,
                ])
                ->add('perscom__report_in__failure_status', StatusType::class, [
                    'label' => 'Failure status',
                    'help' => 'Status to move the user to when they fail to report in. For example: AWOL',
                    'required' => false,
                ])
                // Operations
                ->add('perscom__operations__absent_notification', CheckboxType::class, [
                    'label' => 'Send absent notifications',
                    'required' => false,
                    'help' => 'Send a simple notification when the user is marked absent in an after action report.'
                ])
                ->add('perscom__operations__absent_notification_message', TextType::class, [
                    'label' => 'Absent notification message',
                    'required' => false,
                    'help' => 'If absent notifications are turned on, and this field is empty, a standard message will be used. Max 300 characters.',
                    'constraints' => [new Assert\Length(max: 300)],
                ])
                ->add('perscom__operations__consecutive_absent_notification', CheckboxType::class, [
                    'label' => 'Send consecutive absence notifications',
                    'required' => false,
                    'help' => 'Send an email and optionally change the user\'s status when they are marked absent multiple times in a row.'
                ])
                ->add('perscom__operations__consecutive_absent_notification_count', NumberType::class, [
                    'label' => 'Consecutive absence count',
                    'required' => false,
                    'help' => 'How many times the user needs to be marked absent in an after action report before being considered consecutively absent. For example, if set to 3, the user will receive their first notification on their third absence.',
                    'constraints' => [new Assert\PositiveOrZero()],
                ])
                ->add('perscom__operations__consecutive_absent_notification_message', RichTextEditorType::class, [
                    'label' => 'Consecutive absence email content',
                    'required' => false,
                    'help' => 'If this is empty, a standard message will be used.',
                ])
                ->add('perscom__operations__consecutive_absent_status', StatusType::class, [
                    'label' => 'Consecutive absence status',
                    'placeholder' => 'Do not change status',
                    'required' => false,
                    'help' => 'Automatically move users with consecutive absences to a different status.'
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
