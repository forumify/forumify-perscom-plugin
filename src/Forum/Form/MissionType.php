<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Forumify\Core\Form\RichTextEditorType;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'help' => 'Start date and time in UTC.'
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'help' => 'End date and time in UTC.'
            ])
            ->add('sendNotification', CheckboxType::class, [
                'required' => false,
                'help' => 'Send a "new mission posted" notification to everyone who has access to this operation.'
            ])
            ->add('createCombatRecords', CheckboxType::class, [
                'required' => false,
                'help' => 'Automatically create combat records '
            ])
            ->add('combatRecordText', TextType::class, [
                'required' => false,
                'help' => 'Only used if create combat records is enabled. If empty, a default message including the operation and mission will be used.'
            ])
            ->add('briefing', RichTextEditorType::class);
    }
}
