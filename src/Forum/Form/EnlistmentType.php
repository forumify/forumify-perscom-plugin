<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnlistmentType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'perscom_form' => null,
            'data_class' => Enlistment::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, ['disabled' => true])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('additionalFormData', PerscomFormType::class, [
                'label' => false,
                'perscom_form' => $options['perscom_form']
            ]);
    }
}
