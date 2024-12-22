<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Forumify\Calendar\Entity\Calendar;
use Forumify\Core\Entity\User;
use Forumify\Core\Form\RichTextEditorType;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        $builder
            ->add('title')
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'view_timezone' => $user?->getTimezone() ?? 'UTC',
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'view_timezone' => $user?->getTimezone() ?? 'UTC',
            ])
            ->add('calendar', EntityType::class, [
                'required' => false,
                'class' => Calendar::class,
                'choice_label' => 'title',
                'autocomplete' => true,
                'placeholder' => 'Do not create a calendar event',
                'help' => 'Automatically create a calendar event after posting this mission.',
            ])
            ->add('sendNotification', CheckboxType::class, [
                'required' => false,
                'help' => 'Send a "new mission posted" notification to everyone who has access to this operation.',
            ])
            ->add('createCombatRecords', CheckboxType::class, [
                'required' => false,
                'help' => 'Automatically create combat records ',
            ])
            ->add('combatRecordText', TextType::class, [
                'required' => false,
                'help' => 'Only used if create combat records is enabled. If empty, a default message including the operation and mission will be used.',
            ])
            ->add('briefing', RichTextEditorType::class)
        ;
    }
}
