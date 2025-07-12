<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassInstructor;
use Forumify\PerscomPlugin\Perscom\Entity\CourseInstructor;
use Forumify\PerscomPlugin\Perscom\Form\UserType;
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
        $builder
            ->add('perscomUserId', UserType::class, [
                'attr' => ['class' => 'd-none'],
                'autocomplete' => true,
                'label' => false,
                'placeholder' => 'Please select a user',
            ])
            ->add('present', CheckboxType::class, [
                'required' => false,
            ])
            ->add('instructor', EntityType::class, [
                'autocomplete' => true,
                'choice_label' => 'title',
                'class' => CourseInstructor::class,
                'label' => 'Role',
                'query_builder' => fn (EntityRepository $er): QueryBuilder => $er
                    ->createQueryBuilder('ci')
                    ->andWhere('ci.course = :course')
                    ->setParameter('course', $options['course_class']->getCourse()),
                'required' => false,
            ])
        ;
    }
}
