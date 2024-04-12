<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\Core\Repository\RoleRepository;
use Forumify\Forum\Repository\ForumRepository;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly ForumRepository $forumRepository,
        private readonly RoleRepository $roleRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // general settings
            ->add('perscom__endpoint', TextType::class, [
                'label' => 'Endpoint',
            ])
            ->add('perscom__perscom_id', TextType::class, [
                'label' => 'PERSCOM ID',
                'help' => 'Can be found on your PERSCOM dashboard, under "System" > "Settings".',
            ])
            ->add('perscom__api_key', PasswordType::class, [
                'label' => 'API Key',
                'help' => 'Create a new API key for forumify on your PERSCOM dashboard, under "System" > "API" > "Keys". Remember to add all scopes!',
                'required' => false,
            ])
            // enlistment
            ->add('perscom__enlistment__status', ChoiceType::class, [
                'label' => 'Eligible Status',
                'help' => 'Only users in these statuses can start the enlistment process. Users who do not have a PERSCOM account yet will always be allowed to enlist.',
                'multiple' => true,
                'choices' => $this->safe($this->getStatusChoices(...)),
                'required' => false,
            ])
            ->add('perscom__enlistment__form', ChoiceType::class, [
                'label' => 'Enlistment Form',
                'help' => 'The form to use for enlistments, by default, all required fields to create a PERSCOM user are already added by this plugin.',
                'choices' => $this->safe($this->getFormChoices(...)),
            ])
            ->add('perscom__enlistment__forum', ChoiceType::class, [
                'label' => 'Target Forum',
                'help' => 'Automatically post a topic containing the enlistment to this forum.',
                'required' => false,
                'choices' => $this->getForumChoices(),
                'placeholder' => 'Do not create enlistment topics'
            ])
            ->add('perscom__enlistment__role', ChoiceType::class, [
                'label' => 'User Role',
                'help' => 'Automatically assign this role to the user upon creating an enlistment.',
                'required' => false,
                'choices' => $this->getRoleChoices(),
                'placeholder' => 'Do not assign a role'
            ]);
    }

    private function getStatusChoices(): array
    {
        $statuses = $this->perscomFactory
            ->getPerscom()
            ->statuses()
            ->all(limit: 100)
            ->json('data') ?? [];

        return $this->toChoiceArray($statuses);
    }

    private function getFormChoices(): array
    {
        $forms = $this->perscomFactory
            ->getPerscom()
            ->forms()
            ->all(limit: 100)
            ->json('data') ?? [];

        return $this->toChoiceArray($forms);
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

    private function toChoiceArray(array $options): array
    {
        $choices = ['None' => null];
        foreach ($options as $option) {
            $choices[$option['name']] = $option['id'];
        }
        return $choices;
    }

    private function safe(callable $fn): array
    {
        try {
            return $fn();
        } catch (\Exception) {
        }
        return [];
    }
}
