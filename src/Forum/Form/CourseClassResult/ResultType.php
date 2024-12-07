<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form\CourseClassResult;

use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResultType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('class');
        $resolver->setAllowedTypes('class', [CourseClass::class]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var CourseClass $class */
        $class = $options['class'];

        $builder
            ->add('instructors', InstructorsType::class, ['class' => $class])
            ->add('instructor_service_record', CheckboxType::class, [
                'required' => false,
                'help' => 'Automatically create service records for the present instructors in this class.',
            ])
            ->add('students', StudentsType::class, ['class' => $class])
            ->add('student_service_record', CheckboxType::class, [
                'required' => false,
                'help' => 'Automatically create service records for all non-excused students in this class.',
            ])
        ;
    }
}
