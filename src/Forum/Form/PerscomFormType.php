<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PerscomFormType extends AbstractType
{
    private const FIELD_MAP = [
        'boolean' => CheckboxType::class,
        'code' => TextareaType::class,
        'color' => ColorType::class,
        'country' => CountryType::class,
        'date' => DateType::class,
        'datetime-local' => DateTimeType::class,
        'email' => EmailType::class,
        'file' => FileType::class,
        'number' => NumberType::class,
        'password' => PasswordType::class,
        'select' => ChoiceType::class,
        'text' => TextType::class,
        'textarea' => TextareaType::class,
        'timezone' => TimezoneType::class,
    ];

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'perscom_form' => null,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['perscom_form']['fields'] ?? [] as $field) {
            $type = self::FIELD_MAP[$field['type']];

            $fieldOptions = [
                'label' => $field['name'],
                'help' => $field['help'],
                'required' => $field['required'],
                'disabled' => $field['readonly'],
            ];

            if ($type === ChoiceType::class) {
                $fieldOptions['choices'] = array_flip($field['options']);
            }

            if ($type === DateType::class || $type === DateTimeType::class) {
                $fieldOptions['years'] = range(1900, (int)(new \DateTime())->format('Y'));
            }

            $builder->add($field['key'], $type, $fieldOptions);
        }
    }
}
