<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Exception;
use League\CommonMark\CommonMarkConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('perscom_markdown', $this->markdown(...), ['is_safe' => ['html']]),
        ];
    }

    private function markdown(string $input): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        try {
            return $converter->convert($input)->getContent();
        } catch (Exception) {
            return 'An error occurred converting markdown to html.';
        }
    }
}