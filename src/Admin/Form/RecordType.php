<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\Form as PerscomForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

        $builder->add('text', TextareaType::class, [
            'required' => false,
            'empty_data' => '',
        ]);
    }

    private function addAwardFields(FormBuilderInterface $builder): void
    {
        $builder->add('award_id', PerscomForm\AwardType::class, [
            'label' => 'Award',
        ]);
    }

    private function addRankFields(FormBuilderInterface $builder): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Promote' => 0,
                    'Demote' => 1,
                ],
            ])
            ->add('rank_id', PerscomForm\RankType::class, [
                'label' => 'Rank',
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
            ])
            ->add('status_id', PerscomForm\StatusType::class, [
                'label' => 'Status',
                'required' => false,
            ])
            ->add('specialty_id', PerscomForm\SpecialtyType::class, [
                'label' => 'Specialty',
                'required' => false,
            ])
            ->add('unit_id', PerscomForm\UnitType::class, [
                'label' => 'Unit',
            ])
            ->add('position_id', PerscomForm\PositionType::class, [
                'label' => 'Position',
            ]);
    }

    private function addQualificationFields(FormBuilderInterface $builder): void
    {
        $builder->add('qualification_id', PerscomForm\QualificationType::class, [
            'label' => 'Qualification',
        ]);
    }
}
