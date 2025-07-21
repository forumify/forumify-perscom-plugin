<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom;

use Forumify\PerscomPlugin\Perscom\Entity\Roster;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class RosterFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Roster::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->word(),
            'perscomId' => self::faker()->unique()->numberBetween(1, 1000),
        ];
    }
}
