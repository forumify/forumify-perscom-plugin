<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form\CourseClassResult;

use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Form\QualificationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('class');
        $resolver->setAllowedTypes('class', [CourseClass::class]);
        $resolver->define('student');
        $resolver->setAllowedTypes('student', 'array');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var CourseClass $class */
        $class = $options['class'];

        $builder
            ->add('result', ChoiceType::class, [
                'placeholder' => 'Please select a result',
                'choices' => [
                    'Passed' => 'passed',
                    'Failed' => 'failed',
                    'Excused' => 'excused',
                    'No Show' => 'no-show',
                ],
            ])
            ->add('qualifications', QualificationType::class, [
                'autocomplete' => true,
                'multiple' => true,
                'required' => false,
                'choice_filter' => fn ($id) => in_array($id, $class->getCourse()->getQualifications(), true)
            ])
        ;
    }
}
