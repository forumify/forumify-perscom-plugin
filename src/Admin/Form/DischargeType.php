<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DischargeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PerscomUser::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data_class'];
        $builder
            ->add('discharge_level', ChoiceType::class, [
                'choices' => [
                    'General Discharge' => 'General Discharge',
                    'Honorable Discharge' => 'Honorable Discharge',
                    'Other Than Honorable Discharge' => 'Other Than Honorable Discharge',
                    'Retirement' => 'Retirement',
                ],
                'mapped' => false,
            ])
            ->add('reason', TextType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('rank', EntityType::class, [
                'class' => Rank::class,
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('unit', EntityType::class, [
                'class' => Unit::class,
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('position', EntityType::class, [
                'class' => Position::class,
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
                'required' => false,
            ]);
    }
}
