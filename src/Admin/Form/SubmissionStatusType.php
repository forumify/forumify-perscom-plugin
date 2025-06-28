<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmissionStatusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
            ])
            ->add('reason', TextareaType::class, [
                'required' => false,
                'empty_data' => '',
            ])
            ->add('sendNotification', CheckboxType::class, [
                'required' => false,
                'data' => true,
            ]);
    }
}
