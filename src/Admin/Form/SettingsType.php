<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\Core\Repository\SettingRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType
{
    public function __construct(private readonly SettingRepository $settingRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('perscom_id', TextType::class, [
            'data' => $this->settingRepository->get('perscom.perscom_id'),
            'label' => 'PERSCOM ID',
            'help' => 'Can be found on your PERSCOM dashboard, under "System" > "Settings".',
        ]);

        $builder->add('api_key', PasswordType::class, [
            'attr' => [
                'value' => $this->settingRepository->get('perscom.api_key'),
            ],
            'help' => 'Create a new API key for forumify on your PERSCOM dashboard, under "System" > "API" > "Keys". Remember to add all scopes!',
        ]);
    }
}
