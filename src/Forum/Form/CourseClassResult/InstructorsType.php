<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form\CourseClassResult;

use Exception;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\FilterObject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstructorsType extends AbstractType
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('class');
        $resolver->setAllowedTypes('class', [CourseClass::class]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $instructorIds = $options['class']->getInstructors();
        try {
            $instructors = $this->perscomFactory
                ->getPerscom()
                ->users()
                ->search(filter: new FilterObject('id', 'in', $instructorIds))
                ->json('data');
        } catch (Exception) {
            $instructors = [];
        }

        foreach ($instructors as $instructor) {
            $builder->add((string)$instructor['id'], CheckboxType::class, [
                'label' => "<strong>{$instructor['name']}</strong> attended",
                'label_html' => true,
                'required' => false,
            ]);
        }
    }
}
