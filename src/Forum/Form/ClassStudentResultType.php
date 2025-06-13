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
            'data_class' => CourseClassStudent::class,
            'course_class' => null,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $class = $options['course_class'];

        $builder
            ->add('perscomUserId', UserType::class, [
                'autocomplete' => true,
                'label' => false,
                'placeholder' => 'Please select a user',
                'attr' => ['class' => 'd-none']
            ])
            ->add('result', ChoiceType::class, [
                'choices' => [
                    'Passed' => 'passed',
                    'Failed' => 'failed',
                    'Excused' => 'excused',
                    'No Show' => 'no-show',
                ]
            ])
            ->add('qualifications', QualificationType::class, [
                'multiple' => true,
                'autocomplete' => true,
                'required' => false,
                'choice_filter' => fn ($id) => in_array($id, $class->getCourse()->getQualifications(), true)
            ])
            ->add('serviceRecordTextOverride', TextType::class, [
                'required' => false,
            ])
        ;
    }
}
