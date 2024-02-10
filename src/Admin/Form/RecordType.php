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
            ->add('specialty_id', PerscomForm\SpecialtyType::class, [
                'label' => 'Specialty',
            ])
            ->add('status_id', PerscomForm\StatusType::class, [
                'label' => 'Status',
            ])
            ->add('unit_id', PerscomForm\UnitType::class, [
                'label' => 'Primary Unit',
            ])
            ->add('secondary_unit_ids', PerscomForm\UnitType::class, [
                'label' => 'Secondary Units',
                'multiple' => true,
                'required' => false,
            ])
            ->add('position_id', PerscomForm\PositionType::class, [
                'label' => 'Primary Position',
            ])
            ->add('secondary_position_ids', PerscomForm\PositionType::class, [
                'label' => 'Secondary Positions',
                'multiple' => true,
                'required' => false,
            ]);
    }

    private function addQualificationFields(FormBuilderInterface $builder): void
    {
        $builder->add('qualification_id', PerscomForm\QualificationType::class, [
            'label' => 'Qualification',
        ]);
    }
}
