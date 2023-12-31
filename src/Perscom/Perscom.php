<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom;

use Perscom\PerscomConnection;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(autowire: false)]
class Perscom extends PerscomConnection
{
    public function __construct(private readonly string $endpoint, string $apiKey, string $perscomId)
    {
        parent::__construct($apiKey, $perscomId);
    }

    public function resolveBaseUrl(): string
    {
        return $this->endpoint;
    }

    protected function defaultHeaders(): array
    {
        $headers = parent::defaultHeaders();
        return [
            ...$headers,
            'X-Perscom-Notifications' => 'false',
        ];
    }
}
