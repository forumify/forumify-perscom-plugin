<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\Form\PositionType;
use Forumify\PerscomPlugin\Perscom\Form\RankType;
use Forumify\PerscomPlugin\Perscom\Form\SpecialtyType;
use Forumify\PerscomPlugin\Perscom\Form\StatusType;
use Forumify\PerscomPlugin\Perscom\Form\UnitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserData::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // general
            ->add('name', TextType::class)
            ->add('email', TextType::class)
            ->add('rank', RankType::class, [
                'help' => 'perscom.admin.users.edit.rank_help'
            ])
            // assignment
            ->add('specialty', SpecialtyType::class)
            ->add('status', StatusType::class)
            ->add('position', PositionType::class)
            ->add('unit', UnitType::class)
            // uniform
            ->add('uniform', FileType::class, [
                'required' => false,
            ])
            ->add('signature', FileType::class, [
                'required' => false,
            ]);
    }
}
