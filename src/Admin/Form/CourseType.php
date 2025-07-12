<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\Core\Form\RichTextEditorType;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\PerscomPlugin\Perscom\Form\QualificationType;
use Forumify\PerscomPlugin\Perscom\Form\RankType;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CourseType extends AbstractType
{
    public function __construct(private readonly Packages $packages)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $imagePreview = empty($options['data']) ? null : $options['data']->getImage();

        $builder
            ->add('title')
            ->add('description', RichTextEditorType::class)
            ->add('newImage', FileType::class, [
                'attr' => [
                    'preview' => $imagePreview
                        ? $this->packages->getUrl($imagePreview, 'forumify.asset')
                        : null,
                ],
                'constraints' => [
                    new Assert\Image(
                        maxSize: '10M',
                    ),
                ],
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
            ])
            ->add('rankRequirement', RankType::class, [
                'autocomplete' => true,
                'help' => 'What is the minimum rank required to take part in this course? (All ranks equal or above will be eligible)',
                'required' => false,
            ])
            ->add('prerequisites', QualificationType::class, [
                'autocomplete' => true,
                'help' => 'Which qualifications are required to take part in this course?',
                'multiple' => true,
                'required' => false,
            ])
            ->add('qualifications', QualificationType::class, [
                'autocomplete' => true,
                'help' => 'Which qualifications can be achieved by completing this course?',
                'multiple' => true,
                'required' => false,
            ]);
    }
}
