<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom;

use Perscom\PerscomConnection;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(autowire: false)]
class Perscom extends PerscomConnection
{
    public const DATE_FORMAT = 'Y-m-d\TH:i:s.u\Z';

    public function __construct(
        private readonly string $endpoint,
        string $apiKey,
        string $perscomId,
        private readonly bool $bypassCache = false,
    ) {
        parent::__construct($apiKey, $perscomId);
    }

    public function resolveBaseUrl(): string
    {
        return $this->endpoint;
    }

    protected function defaultHeaders(): array
    {
        $headers = [
            ...parent::defaultHeaders(),
            'X-Perscom-Notifications' => 'false',
        ];

        if ($this->bypassCache) {
            $headers['X-Perscom-Bypass-Cache'] = true;
        }

        return $headers;
    }
}
