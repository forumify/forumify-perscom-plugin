<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomSyncResult;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PerscomSyncResult>
 */
class SyncResultFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return PerscomSyncResult::class;
    }

    protected function defaults(): array|callable
    {
        return [];
    }
}
