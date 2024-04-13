<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PerscomExtension extends AbstractExtension
{
    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('perscom', fn () => $this->perscomFactory->getPerscom()),
        ];
    }
}
