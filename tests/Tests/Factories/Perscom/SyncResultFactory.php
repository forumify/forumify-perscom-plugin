<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomSyncResult;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<PerscomSyncResult>
 */
class SyncResultFactory extends PersistentObjectFactory
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
