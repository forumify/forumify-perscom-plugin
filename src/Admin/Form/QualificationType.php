<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\Core\Form\RichTextEditorType;
use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class QualificationType extends AbstractType
{
    public function __construct(private readonly Packages $packages)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Qualification::class,
            'image_required' => false,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $imagePreview = empty($options['data']) ? null : $options['data']->getImage();

        $builder
            ->add('name', TextType::class)
            ->add('description', RichTextEditorType::class, [
                'required' => false
            ])
            ->add('newImage', FileType::class, [
                'mapped' => false,
                'label' => 'Image',
                'help' => 'Recommended size is 250x250.',
                'attr' => [
                    'preview' => $imagePreview
                        ? $this->packages->getUrl($imagePreview, 'perscom.asset')
                        : null,
                ],
                'constraints' => [
                    ...($options['image_required'] ? [
                        new Assert\NotBlank(allowNull: false),
                    ]: []),
                    new Assert\Image(
                        maxSize: '10M',
                    ),
                ],
            ])
        ;
    }
}
