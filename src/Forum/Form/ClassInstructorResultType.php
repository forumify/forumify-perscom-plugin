<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassInstructor;
use Forumify\PerscomPlugin\Perscom\Entity\CourseInstructor;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassInstructorResultType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'course_class' => null,
            'data_class' => CourseClassInstructor::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var CourseClass $class */
        $class = $options['course_class'];

        $builder
            ->add('user', EntityType::class, [
                'attr' => ['class' => 'd-none'],
                'autocomplete' => true,
                'choice_label' => 'name',
                'class' => PerscomUser::class,
                'label' => false,
                'placeholder' => 'Please select a user',
            ])
            ->add('present', CheckboxType::class, [
                'required' => false,
            ])
            ->add('instructor', EntityType::class, [
                'autocomplete' => true,
                'choices' => $class->getCourse()->getInstructors(),
                'choice_label' => 'title',
                'class' => CourseInstructor::class,
                'label' => 'Role',
                'required' => false,
            ])
        ;
    }
}
