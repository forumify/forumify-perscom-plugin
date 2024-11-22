<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Forumify\Core\Form\RichTextEditorType;
use Forumify\PerscomPlugin\Perscom\Entity\AfterActionReport;
use Forumify\PerscomPlugin\Perscom\Form\UnitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AfterActionReportType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AfterActionReport::class,
            'allow_extra_fields' => true,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('unitId', UnitType::class, [
                'label' => 'Unit',
            ])
            ->add('attendanceJson', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('report', RichTextEditorType::class);
    }
}
