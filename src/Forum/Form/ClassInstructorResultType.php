<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassInstructor;
use Forumify\PerscomPlugin\Perscom\Entity\CourseInstructor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassInstructorResultType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CourseClassInstructor::class,
            'course_class' => null,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('perscomUserId', HiddenType::class)
            ->add('present', CheckboxType::class, [
                'required' => false,
            ])
            ->add('instructor', EntityType::class, [
                'class' => CourseInstructor::class,
                'label' => 'Role',
                'required' => false,
                'choice_label' => 'title',
                'autocomplete' => true,
                'query_builder' => fn (EntityRepository $er): QueryBuilder => $er
                    ->createQueryBuilder('ci')
                    ->andWhere('ci.course = :course')
                    ->setParameter('course', $options['course_class']->getCourse())
            ])
        ;
    }
}
