<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom;

use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class QualificationFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Qualification::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'perscomId' => self::faker()->unique()->numberBetween(1, 1000),
        ];
    }
}
