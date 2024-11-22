<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Forumify\Core\Form\RichTextEditorType;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('briefing', RichTextEditorType::class);
    }
}
