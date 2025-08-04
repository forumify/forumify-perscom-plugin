<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\Core\Form\RichTextEditorType;
use Forumify\PerscomPlugin\Forum\Form\PerscomFormType;
use Forumify\PerscomPlugin\Perscom\Entity\FormField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\String\u;

class FormFieldType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormField::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $typeOptions = [];
        foreach (array_keys(PerscomFormType::FIELD_MAP) as $type) {
            $typeOptions[u($type)->replace('-', ' ')->title(false)->toString()] = $type;
        }

        $builder
            ->add('label', TextType::class)
            ->add('type', ChoiceType::class, [
                'choices' => $typeOptions,
            ])
            ->add('help', RichTextEditorType::class, [
                'required' => false,
            ])
            ->add('required', CheckboxType::class, [
                'required' => false,
            ])
            ->add('readonly', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }
}
