<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
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
        private readonly SettingRepository $settingRepository,
        private readonly PerscomFactory $perscomFactory,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $statuses = $this->perscomFactory
            ->getPerscom()
            ->statuses()
            ->all(limit: 100)
            ->json('data') ?? [];

        $statusChoices = array_combine(
            array_column($statuses, 'name'),
            array_column($statuses, 'id'),
        );

        $builder
            // general settings
            ->add('settings__endpoint', TextType::class, [
                'label' => 'Endpoint',
                'data' => $this->settingRepository->get('perscom.endpoint'),
            ])
            ->add('settings__perscom_id', TextType::class, [
                'label' => 'PERSCOM ID',
                'data' => $this->settingRepository->get('perscom.perscom_id'),
                'help' => 'Can be found on your PERSCOM dashboard, under "System" > "Settings".',
            ])
            ->add('settings__api_key', PasswordType::class, [
                'label' => 'API Key',
                'attr' => [
                    'value' => $this->settingRepository->get('perscom.api_key'),
                ],
                'help' => 'Create a new API key for forumify on your PERSCOM dashboard, under "System" > "API" > "Keys". Remember to add all scopes!',
            ])
            // activity tracker
            ->add('activity_tracker__status_to_track', ChoiceType::class, [
                'label' => 'Status to track',
                'multiple' => true,
                'choices' => $statusChoices,
                'data' => $this->settingRepository->getJson('perscom.activity_tracker.status_to_track')
            ])
            ->add('activity_tracker__time_until_inactive', NumberType::class, [
                'label' => 'Days until inactive',
                'data' => $this->settingRepository->get('perscom.activity_tracker.time_until_inactive')
            ])
            ->add('activity_tracker__inactive_status', ChoiceType::class, [
                'label' => 'Inactive status',
                'choices' => $statusChoices,
                'data' => $this->settingRepository->get('perscom.activity_tracker.inactive_status')
            ]);
    }
}
