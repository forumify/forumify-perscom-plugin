<?php

declare(strict_types=1);

namespace PluginTests;

use Forumify\Core\ForumifyKernel;

class PluginTestKernel extends ForumifyKernel
{
    public function __construct(string $env, bool $debug = false)
    {
        $context = ['APP_ENV' => $env, 'APP_DEBUG' => $debug];
        parent::__construct($context, dirname(__DIR__));
    }
}
