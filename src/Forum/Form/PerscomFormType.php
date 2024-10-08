<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use DateTime;
use Forumify\PerscomPlugin\Perscom\Perscom;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
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
use Traversable;

class PerscomFormType extends AbstractType implements DataMapperInterface
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'perscom_form' => null,
            'allowed_types' => [],
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['perscom_form']['fields'] ?? [] as $field) {
            if (!empty($options['allowed_types']) && !in_array($field['type'], $options['allowed_types'], true)) {
                continue;
            }

            $type = self::FIELD_MAP[$field['type']];

            $fieldOptions = [
                'label' => $field['name'],
                'help' => $field['help'],
                'help_html' => true,
                'required' => $field['required'],
                'disabled' => $options['disabled'] || $field['readonly'],
            ];

            if ($type === ChoiceType::class) {
                $fieldOptions['choices'] = array_flip($field['options']);
            }

            if ($type === DateType::class || $type === DateTimeType::class) {
                $fieldOptions['widget'] = 'single_text';
            }

            $builder->add($field['key'], $type, $fieldOptions);
        }

        $builder->setDataMapper($this);
    }

    /** @inheritDoc */
    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        foreach ($forms as $field => $form) {
            if ($form->getConfig()->getDataClass()) {
                continue;
            }

            if ($viewData[$field] ?? false) {
                $form->setData($viewData[$field]);
            }
        }
    }

    /** @inheritDoc */
    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        foreach ($forms as $field => $form) {
            $value = $form->getData();
            if ($value instanceof DateTime) {
                $value = $value->format(Perscom::DATE_FORMAT);
            }

            $viewData[$field] = $value;
        }
    }
}
