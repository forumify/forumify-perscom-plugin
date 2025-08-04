<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use DateTime;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('perscom_date', $this->perscomDate(...)),
        ];
    }

    private function perscomDate(string $value): ?DateTime
    {
        return DateTime::createFromFormat(Perscom::DATE_FORMAT, $value) ?: null;
    }
}
