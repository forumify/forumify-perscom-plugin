<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Forumify\PerscomPlugin\Perscom\Form\UserType;
use Forumify\PerscomPlugin\Perscom\Form\AwardType;
use Forumify\PerscomPlugin\Perscom\Entity\AwardNomination;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AwardNominationForm extends AbstractType
{
    public function __construct(
        private readonly Packages $packages,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AwardNomination::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('receiverUserId', UserType::class, [
                'required' => true
            ])
            ->add('awardId', AwardType::class, [
                'required' => true 
            ])
            ->add('reason', TextareaType::class, [
                'required' => true
            ]);
    }
}
