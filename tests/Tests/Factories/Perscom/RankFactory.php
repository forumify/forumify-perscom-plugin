<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom;

use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

class RankFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Rank::class;
    }

    protected function defaults(): array|callable
    {
        return ['perscomId' => self::faker()->unique()->numberBetween(0, 1000)];
    }
}
