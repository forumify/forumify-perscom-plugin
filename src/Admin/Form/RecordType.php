<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Award;
use Forumify\PerscomPlugin\Perscom\Entity\Document;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Forumify\PerscomPlugin\Perscom\Entity\Specialty;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecordType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'type' => 'service',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('users', EntityType::class, [
                'multiple' => true,
                'autocomplete' => true,
                'class' => PerscomUser::class,
                'choice_label' => 'name',
            ])
            ->add('created_at', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ]);

        switch ($options['type']) {
            case 'award':
                $this->addAwardFields($builder);
                break;
            case 'rank':
                $this->addRankFields($builder);
                break;
            case 'assignment':
                $this->addAssignmentFields($builder);
                break;
            case 'qualification':
                $this->addQualificationFields($builder);
                break;
            default:
                // no-op
        }

        $builder
            ->add('text', TextareaType::class, [
                'required' => false,
                'empty_data' => '',
            ])
            ->add('document', EntityType::class, [
                'required' => false,
                'autocomplete' => true,
                'class' => Document::class,
                'choice_label' => 'name',
            ])
            ->add('sendNotification', CheckboxType::class, [
                'required' => false,
                'data' => true,
            ]);
    }

    private function addAwardFields(FormBuilderInterface $builder): void
    {
        $builder->add('award', EntityType::class, [
            'class' => Award::class,
            'autocomplete' => true,
            'choice_label' => 'name',
        ]);
    }

    private function addRankFields(FormBuilderInterface $builder): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Promote' => 'promote',
                    'Demote' => 'demote',
                ],
            ])
            ->add('rank', EntityType::class, [
                'class'=> Rank::class,
                'autocomplete' => true,
                'choice_label' => 'name',
            ]);
    }

    private function addAssignmentFields(FormBuilderInterface $builder): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Primary' => 'primary',
                    'Secondary' => 'secondary',
                ],
                'placeholder' => 'Select a type',
            ])
            ->add('status', EntityType::class, [
                'required' => false,
                'placeholder' => 'Keep current status.',
                'class' => Status::class,
                'autocomplete' => true,
                'choice_label' => 'name',
            ])
            ->add('specialty', EntityType::class, [
                'required' => false,
                'placeholder' => 'Keep current specialty.',
                'class' => Specialty::class,
                'autocomplete' => true,
                'choice_label' => 'name',
            ])
            ->add('unit', EntityType::class, [
                'class' => Unit::class,
                'autocomplete' => true,
                'choice_label' => 'name',
            ])
            ->add('position', EntityType::class, [
                'class' => Position::class,
                'autocomplete' => true,
                'choice_label' => 'name',
            ]);
    }

    private function addQualificationFields(FormBuilderInterface $builder): void
    {
        $builder->add('qualification', EntityType::class, [
            'class' => Qualification::class,
            'autocomplete' => true,
            'choice_label' => 'name',
        ]);
    }
}
