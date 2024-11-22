<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\Core\Form\RichTextEditorType;
use Forumify\PerscomPlugin\Perscom\Entity\Operation;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OperationType extends AbstractType
{
    public function __construct(
        private readonly Packages $packages,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Operation::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $imagePreview = empty($options['data']) ? null : $options['data']->getImage();

        $builder
            ->add('title')
            ->add('description', TextareaType::class, [
                'help' => 'perscom.admin.operation.description_help'
            ])
            ->add('newImage', FileType::class, [
                'mapped' => false,
                'label' => 'Image',
                'required' => false,
                'attr' => [
                    'preview' => $imagePreview
                        ? $this->packages->getUrl($imagePreview, 'forumify.asset')
                        : null,
                ],
                'constraints' => [
                    new Assert\Image(maxSize: '1M'),
                ],
            ])
            ->add('content', RichTextEditorType::class, [
                'required' => false,
                'empty_data' => '',
            ])
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('missionBriefingTemplate', RichTextEditorType::class, [
                'required' => false,
                'empty_data' => '',
            ])
            ->add('afterActionReportTemplate', RichTextEditorType::class, [
                'required' => false,
                'empty_data' => '',
            ]);
    }
}
