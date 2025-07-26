<?php

declare(strict_types=1);

namespace PluginTests\Factories\Perscom\Record;

use Forumify\PerscomPlugin\Perscom\Entity\Record\QualificationRecord;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class QualificationRecordFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return QualificationRecord::class;
    }

    protected function defaults(): array|callable
    {
        return [];
    }
}
