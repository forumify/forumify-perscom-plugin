<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form\CourseClassResult;

use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Symfony\Component\Form\AbstractType;
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
            ->add('instructors', InstructorsType::class, [
                'class' => $class,
                'required' => false,
            ])
            ->add('students', StudentsType::class, [
                'class' => $class,
                'required' => false,
            ])
        ;
    }
}
