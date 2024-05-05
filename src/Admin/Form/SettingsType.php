<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType
{
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
            ]);
    }
}
