<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\Core\Repository\SettingRepository;
use Forumify\Forum\Entity\Forum;
use Forumify\Forum\Repository\ForumRepository;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly ForumRepository $forumRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $statusChoices = $this->getStatusChoices();
        $formChoices = $this->getFormChoices();
        $forumChoices = [];
        foreach ($this->forumRepository->findAll() as $forum) {
            $forumChoices[$forum->getTitle()] = $forum->getId();
        }

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
                'choices' => $statusChoices,
                'required' => false,
            ])
            ->add('perscom__enlistment__form', ChoiceType::class, [
                'label' => 'Enlistment Form',
                'help' => 'The form to use for enlistments, by default, all required fields to create a PERSCOM user are already added by this plugin.',
                'choices' => $formChoices,
            ])
            ->add('perscom__enlistment__forum', ChoiceType::class, [
                'label' => 'Target Forum',
                'help' => 'The forum in which to post enlistments.',
                'required' => false,
                'choices' => $forumChoices,
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

    private function toChoiceArray(array $options): array
    {
        $choices = ['None' => null];
        foreach ($options as $option) {
            $choices[$option['name']] = $option['id'];
        }
        return $choices;
    }
}
