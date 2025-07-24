<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassStudent;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Forumify\PerscomPlugin\Perscom\Repository\QualificationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassStudentResultType extends AbstractType
{
    public function __construct(private readonly QualificationRepository $qualificationRepository)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'course_class' => null,
            'data_class' => CourseClassStudent::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var CourseClass $class */
        $class = $options['course_class'];
        $qualifications = $this->qualificationRepository->findBy(['id' => $class->getCourse()->getQualifications()]);
        $qualificationChoices = array_combine(
            array_map(fn (Qualification $qualification) => $qualification->getName(), $qualifications),
            array_map(fn (Qualification $qualification) => $qualification->getId(), $qualifications),
        );

        $builder
            ->add('user', EntityType::class, [
                'attr' => ['class' => 'd-none'],
                'autocomplete' => true,
                'choice_label' => 'name',
                'class' => PerscomUser::class,
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
            ->add('qualifications', ChoiceType::class, [
                'autocomplete' => true,
                'choices' => $qualificationChoices,
                'multiple' => true,
                'required' => false,
            ])
            ->add('serviceRecordTextOverride', TextType::class, [
                'required' => false,
            ])
        ;
    }
}
