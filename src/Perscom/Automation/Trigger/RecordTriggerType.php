<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Automation\Trigger;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class RecordTriggerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recordType', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'All Records',
                'choices' => [
                    'Service' => 'service',
                    'Award' => 'award',
                    'Rank' => 'rank',
                    'Qualification' => 'qualification',
                    'Assignment' => 'assignment',
                ],
            ])
        ;
    }
}
