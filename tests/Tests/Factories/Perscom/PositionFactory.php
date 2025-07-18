<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom;

use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class PositionFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Position::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->sentence(),
            'perscomId' => self::faker()->unique()->numberBetween(0, 1000),
        ];
    }
}
