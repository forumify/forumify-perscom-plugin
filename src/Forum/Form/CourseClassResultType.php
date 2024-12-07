<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Forumify\PerscomPlugin\Forum\Form\CourseClassResult\ResultType;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassResult;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseClassResultType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('class');
        $resolver->setAllowedTypes('class', [CourseClass::class]);
        $resolver->setDefaults([
            'data_class' => CourseClassResult::class
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('result', ResultType::class, [
            'class' => $options['class'],
            'label' => false
        ]);
    }
}
