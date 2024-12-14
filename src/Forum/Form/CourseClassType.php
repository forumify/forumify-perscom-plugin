<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Forumify\Core\Form\RichTextEditorType;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseClassType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CourseClass::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', RichTextEditorType::class, [
                'help' => 'perscom.course.class.description_help',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('signupFrom', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('signupUntil', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('instructors', UserType::class, [
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
                'empty_data' => [],
                'help' => 'perscom.course.class.instructors_help'
            ])
            ->add('instructorSlots', NumberType::class, [
                'required' => false,
                'html5' => true,
                'help' => 'perscom.course.class.instructor_slots_help'
            ])
            ->add('students', UserType::class, [
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
                'empty_data' => [],
                'help' => 'perscom.course.class.students_help'
            ])
            ->add('studentSlots', NumberType::class, [
                'required' => false,
                'html5' => true,
                'help' => 'perscom.course.class.student_slots_help'
            ]);

        if ($options['data']?->getResult()) {
            $builder->add('result', CourseClassResultType::class, [
                'class' => $options['data']
            ]);
        }
    }
}
