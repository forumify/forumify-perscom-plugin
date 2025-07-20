<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom;

use Forumify\PerscomPlugin\Perscom\Entity\FormField;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class FormFieldFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return FormField::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'readonly' => false,
            'required' => false,
            'type' => 'text',
        ];
    }
}
