<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Forumify\PerscomPlugin\Perscom\Entity\Specialty;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function __construct(
        private readonly Packages $packages,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PerscomUser::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var PerscomUser|null $user */
        $user = $options['data'] ?? null;

        $builder
            // general
            ->add('name', TextType::class)
            ->add('rank', EntityType::class, [
                'class' => Rank::class,
                'choice_label' => 'name',
                'required' => false,
                'help' => 'perscom.admin.users.edit.rank_help',
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'help' => 'perscom.admin.users.edit.created_at_help',
            ])
            // assignment
            ->add('specialty', EntityType::class, [
                'class' => Specialty::class,
                'choice_label' => 'name',
                'required' => false,
                'disabled' => true,
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
                'required' => false,
                'disabled' => true,
            ])
            ->add('position', EntityType::class, [
                'class' => Position::class,
                'choice_label' => 'name',
                'required' => false,
                'disabled' => true,
            ])
            ->add('unit', EntityType::class, [
                'class' => Unit::class,
                'choice_label' => 'name',
                'required' => false,
                'disabled' => true,
            ])
            // uniform
            ->add('newUniform', FileType::class, [
                'mapped' => false,
                'label' => 'Uniform',
                'required' => false,
                'attr' => [
                    'preview' => $user?->getUniform()
                        ? $this->packages->getUrl($user->getUniform(), 'perscom.asset')
                        : null
                ],
                'constraints' => [
                    new Assert\Image(
                        maxSize: '1M',
                    ),
                ],
            ])
            ->add('newSignature', FileType::class, [
                'mapped' => false,
                'label' => 'Signature',
                'required' => false,
                'attr' => [
                    'preview' => $user?->getSignature()
                        ? $this->packages->getUrl($user->getSignature(), 'perscom.asset')
                        : null
                ],
                'constraints' => [
                    new Assert\Image(
                        maxSize: '1M',
                    ),
                ],
            ])
        ;
    }
}
