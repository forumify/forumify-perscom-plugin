<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom;

use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class FormFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Form::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->sentence(3),
            'perscomId' => self::faker()->unique()->numberBetween(1, 1000),
        ];
    }
}
