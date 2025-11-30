<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom;

use Forumify\PerscomPlugin\Perscom\Entity\Operation;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Operation>
 */
class OperationFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Operation::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(3),
            'description' => self::faker()->paragraph(),
            'content' => self::faker()->paragraph(),
        ];
    }
}
