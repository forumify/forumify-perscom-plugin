<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom;

use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class StatusFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Status::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'color' => self::faker()->hexColor(),
            'perscomId' => self::faker()->unique()->numberBetween(1, 1000),
        ];
    }
}
