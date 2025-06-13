<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Twig\PerscomCourseExtensionRuntime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassResultType extends AbstractType
{
    public function __construct(private readonly PerscomCourseExtensionRuntime $courseExtension)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CourseClass::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('students', CollectionType::class, [
                'entry_type' => ClassStudentResultType::class,
                'entry_options' => [
                    'course_class' => $options['data'],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('instructors', CollectionType::class, [
                'entry_type' => ClassInstructorResultType::class,
                'entry_options' => [
                    'course_class' => $options['data'],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $instructors = $view->children['instructors'];
        $this->setLabels($instructors->children);

        $students = $view->children['students'];
        $this->setLabels($students->children);
    }

    /**
     * @param array<FormView> $views
     */
    private function setLabels(array $views): void
    {
        $data = [];
        foreach ($views as $view) {
            $data[] = $view->vars['data'];
        }

        $users = $this->courseExtension->getUsers(new ArrayCollection($data));

        foreach ($views as $view) {
            $id = $view->vars['data']->getPerscomUserId();
            $user = $users[$id] ?? null;
            if ($user === null) {
                continue;
            }

            $view->vars['label_html'] = true;
            $view->vars['label'] = "<span class='flex items-center gap-1 mb-2'>
                <img width='24px' height='24px' src='{$user['rankImage']}'>
                {$user['name']}
            </span>";
        }
    }
}
