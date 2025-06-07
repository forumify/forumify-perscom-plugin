<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Forumify\Calendar\Entity\Calendar;
use Forumify\Core\Entity\User;
use Forumify\Core\Form\RichTextEditorType;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseClassType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CourseClass::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        $builder
            ->add('title', TextType::class)
            ->add('description', RichTextEditorType::class, [
                'help' => 'perscom.course.class.description_help',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('signupFrom', DateTimeType::class, [
                'widget' => 'single_text',
                'view_timezone' => $user?->getTimezone() ?? 'UTC',
            ])
            ->add('signupUntil', DateTimeType::class, [
                'widget' => 'single_text',
                'view_timezone' => $user?->getTimezone() ?? 'UTC',
            ])
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'view_timezone' => $user?->getTimezone() ?? 'UTC',
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'view_timezone' => $user?->getTimezone() ?? 'UTC',
            ])
            ->add('calendar', EntityType::class, [
                'required' => false,
                'class' => Calendar::class,
                'choice_label' => 'title',
                'autocomplete' => true,
                'placeholder' => 'Do not create a calendar event',
                'help' => 'Automatically create a calendar event after posting this class.',
            ])
            ->add('studentSlots', NumberType::class, [
                'required' => false,
                'html5' => true,
                'help' => 'perscom.course.class.student_slots_help'
            ]);
    }
}
