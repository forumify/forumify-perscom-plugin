<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Forumify\PerscomPlugin\Perscom\Entity\CourseClassStudent;
use Forumify\PerscomPlugin\Perscom\Form\QualificationType;
use Forumify\PerscomPlugin\Perscom\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassStudentResultType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'course_class' => null,
            'data_class' => CourseClassStudent::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $class = $options['course_class'];

        $builder
            ->add('perscomUserId', UserType::class, [
                'attr' => ['class' => 'd-none'],
                'autocomplete' => true,
                'label' => false,
                'placeholder' => 'Please select a user',
            ])
            ->add('result', ChoiceType::class, [
                'choices' => [
                    'Excused' => 'excused',
                    'Failed' => 'failed',
                    'No Show' => 'no-show',
                    'Passed' => 'passed',
                ],
            ])
            ->add('qualifications', QualificationType::class, [
                'autocomplete' => true,
                'choice_filter' => fn ($id) => in_array($id, $class->getCourse()->getQualifications(), true),
                'multiple' => true,
                'required' => false,
            ])
            ->add('serviceRecordTextOverride', TextType::class, [
                'required' => false,
            ])
        ;
    }
}
