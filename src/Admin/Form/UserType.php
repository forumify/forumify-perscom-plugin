<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\Form\PositionType;
use Forumify\PerscomPlugin\Perscom\Form\RankType;
use Forumify\PerscomPlugin\Perscom\Form\SpecialtyType;
use Forumify\PerscomPlugin\Perscom\Form\StatusType;
use Forumify\PerscomPlugin\Perscom\Form\UnitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserData::class,
            'user' => null,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'] ?? null;

        $builder
            // general
            ->add('name', TextType::class)
            ->add('email', TextType::class, [
                'disabled' => true,
                'help' => 'perscom.admin.users.edit.email_help'
            ])
            ->add('rank', RankType::class, [
                'required' => false,
                'help' => 'perscom.admin.users.edit.rank_help'
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'help' => 'perscom.admin.users.edit.created_at_help'
            ])
            // assignment
            ->add('specialty', SpecialtyType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->add('status', StatusType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->add('position', PositionType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->add('unit', UnitType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->add('secondaryAssignments', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'label' => false,
                'allow_delete' => true,
                'required' => false,
            ])
            // uniform
            ->add('uniform', FileType::class, [
                'required' => false,
                'attr' => [
                    'preview' => $user['cover_photo_url'] ?? null
                ],
            ])
            ->add('signature', FileType::class, [
                'required' => false,
                'attr' => [
                    'preview' => $user['profile_photo_url'] ?? null
                ],
            ]);
    }
}
