<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form\CourseClassResult;

use Exception;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\FilterObject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentsType extends AbstractType
{
    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('class');
        $resolver->setAllowedTypes('class', [CourseClass::class]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $studentIds = $options['class']->getStudents();
        try {
            $students = $this->perscomFactory
                ->getPerscom()
                ->users()
                ->search(filter: new FilterObject('id', 'in', $studentIds))
                ->json('data');
        } catch (Exception) {
            $students = [];
        }

        foreach ($students as $student) {
            $builder->add((string)$student['id'], StudentType::class, [
                'class' => $options['class'],
                'student' => $student,
                'label' => $student['name'],
            ]);
        }
    }
}
