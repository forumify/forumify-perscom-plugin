<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\FilterObject;
use Perscom\Data\SortObject;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PerscomExtension extends AbstractExtension
{
    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('perscom', $this->perscomFactory->getPerscom(...)),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('perscom_filter', $this->createFilter(...)),
            new TwigFilter('perscom_sort', $this->createSort(...)),
        ];
    }

    private function createFilter(array $filter): FilterObject
    {
        return new FilterObject(
            $filter['field'],
            $filter['operator'],
            $filter['value'],
            $filter['type'] ?? 'or',
        );
    }

    private function createSort(array $sort): SortObject
    {
        return new SortObject(
            $sort['field'],
            $sort['direction'] ?? 'asc',
        );
    }
}
