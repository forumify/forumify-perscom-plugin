<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StatusColorExtension extends AbstractExtension
{
    // The color is often the same, so this saves some work
    private array $memo;

    public function getFilters(): array
    {
        return [
            new TwigFilter('perscom_text_color', $this->getTextColor(...)),
        ];
    }

    private function getTextColor(string $color): string
    {
        if (isset($this->memo[$color])) {
            return $this->memo[$color];
        }

        $hex = substr($color, 1);

        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        $this->memo[$color] = $l < 0.5 ? 'white' : 'black';
        return $this->memo[$color];
    }
}
