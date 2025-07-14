<?php

declare(strict_types=1);

namespace PluginTests\Factories\Forumify;

use Forumify\Calendar\Entity\Calendar;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CalendarFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Calendar::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(3),
            'color' => self::faker()->hexColor(),
        ];
    }
}
