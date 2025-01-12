<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form\CourseClassResult;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstructorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('instructor');
        $resolver->setAllowedTypes('instructor', 'array');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('attended', CheckboxType::class, [
                'required' => false,
            ])
            ->add('service_record_text', TextType::class, [
                'required' => false,
                'help' => 'Leave blank to not create a service record',
            ])
        ;
    }
}

