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
        string $apiKey,
        ?string $endpoint = null,
    ) {
        parent::__construct($apiKey, baseUrl: $endpoint);
    }

    protected function defaultHeaders(): array
    {
        return [
            ...parent::defaultHeaders(),
            'X-Perscom-Bypass-Cache' => 'true',
            'X-Perscom-Notifications' => 'false',
        ];
    }
}
