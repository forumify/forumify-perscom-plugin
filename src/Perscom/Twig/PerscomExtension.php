<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\FilterObject;
use Perscom\Data\ScopeObject;
use Perscom\Data\SortObject;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @deprecated These functions exist to call the PERSCOM API from Twig,
 *             Reading data from the API instead of the local DB is deprecated
 *             and will be removed in version 3.
 */
class PerscomExtension extends AbstractExtension
{
    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('perscom', function () {
                trigger_deprecation('forumify/forumify-perscom-plugin', '2.2.5', 'Calling the API directly from twig is deprecated. Use repositories to access the local database instead.');
                return $this->perscomFactory->getPerscom();
            }),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('perscom_filter', $this->createFilter(...)),
            new TwigFilter('perscom_sort', $this->createSort(...)),
            new TwigFilter('perscom_scope', $this->createScope(...)),
        ];
    }

    private function createFilter(array $filter): FilterObject
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.2.5', 'Calling the API directly from twig is deprecated. Use repositories to access the local database instead.');
        return new FilterObject(
            $filter['field'],
            $filter['operator'],
            $filter['value'],
            $filter['type'] ?? 'or',
        );
    }

    private function createSort(array $sort): SortObject
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.2.5', 'Calling the API directly from twig is deprecated. Use repositories to access the local database instead.');
        return new SortObject(
            $sort['field'],
            $sort['direction'] ?? 'asc',
        );
    }

    private function createScope(array $scope): ScopeObject
    {
        trigger_deprecation('forumify/forumify-perscom-plugin', '2.2.5', 'Calling the API directly from twig is deprecated. Use repositories to access the local database instead.');
        return new ScopeObject(
            $scope['name'],
            $scope['parameters'] ?? [],
        );
    }
}
