<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class UserFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return PerscomUser::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->firstNameMale() . ' ' . self::faker()->lastName(),
            'perscomId' => self::faker()->unique()->numberBetween(1, 1000),
        ];
    }
}
