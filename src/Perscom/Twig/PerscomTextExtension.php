<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PerscomTextExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('perscom_text', [PerscomTextExtensionRuntime::class, 'convert'], ['is_safe' => ['html']]),
        ];
    }
}
