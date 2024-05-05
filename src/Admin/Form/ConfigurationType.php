<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\Core\Repository\RoleRepository;
use Forumify\Forum\Repository\ForumRepository;
use Forumify\PerscomPlugin\Perscom\Form\PerscomFormType;
use Forumify\PerscomPlugin\Perscom\Form\StatusType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigurationType extends AbstractType
{
    public function __construct(
        private readonly ForumRepository $forumRepository,
        private readonly RoleRepository $roleRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('perscom__enlistment__status', StatusType::class, [
                'label' => 'Eligible Status',
                'help' => 'Only users in these statuses can start the enlistment process. Users who do not have a PERSCOM account yet will always be allowed to enlist.',
                'multiple' => true,
                'required' => false,
            ])
            ->add('perscom__enlistment__form', PerscomFormType::class, [
                'label' => 'Enlistment Form',
                'help' => 'The form to use for enlistments, by default, all required fields to create a PERSCOM user are already added by this plugin.',
                'placeholder' => 'Select a form to use for enlistments'
            ])
            ->add('perscom__enlistment__forum', ChoiceType::class, [
                'label' => 'Target Forum',
                'help' => 'Automatically post a topic containing the enlistment to this forum.',
                'required' => false,
                'choices' => $this->getForumChoices(),
                'placeholder' => 'Do not create enlistment topics',
            ])
            ->add('perscom__enlistment__role', ChoiceType::class, [
                'label' => 'User Role',
                'help' => 'Automatically assign this role to the user upon creating an enlistment.',
                'required' => false,
                'choices' => $this->getRoleChoices(),
                'placeholder' => 'Do not assign a role',
            ]);
    }

    private function getForumChoices(): array
    {
        $choices = $this->forumRepository
            ->createQueryBuilder('f')
            ->select('f.id', 'f.title')
            ->getQuery()
            ->getArrayResult();

        return array_combine(
            array_column($choices, 'title'),
            array_column($choices, 'id'),
        );
    }

    private function getRoleChoices(): array
    {
        $choices = $this->roleRepository
            ->createQueryBuilder('r')
            ->select('r.id', 'r.title')
            ->getQuery()
            ->getArrayResult();

        return array_combine(
            array_column($choices, 'title'),
            array_column($choices, 'id'),
        );
    }
}
