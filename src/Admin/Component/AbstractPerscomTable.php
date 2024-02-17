<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractTable;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\FilterObject;
use Perscom\Data\SortObject;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractPerscomTable extends AbstractTable
{
    private PerscomFactory $perscomFactory;
    private ?array $data = null;

    #[Required]
    public function setPerscomFactory(PerscomFactory $perscomFactory): void
    {
        $this->perscomFactory = $perscomFactory;
    }

    abstract protected function getResource(): string;

    protected function getInclude(): array
    {
        return [];
    }

    protected function getData(int $limit, int $offset, array $search, array $sort): array
    {
        return $this->fetch()['data'] ?? [];
    }

    protected function getTotalCount(array $search): int
    {
        return $this->fetch()['meta']['total'] ?? 0;
    }

    private function fetch()
    {
        if ($this->data !== null) {
            return $this->data;
        }

        $resource = $this->getResource();
        $perscom = $this->perscomFactory->getPerscom();

        if (!(method_exists($perscom, $resource) && method_exists($perscom->$resource(), 'search'))) {
            throw new \LogicException("Cannot call search on $resource");
        }

        $this->data = $perscom
            ->$resource()
            ->search(
                sort: $this->getSort(),
                filter: $this->getFilters(),
                include: $this->getInclude(),
                page: $this->page,
                limit: $this->limit,
            )
            ->json();
        return $this->data;
    }

    protected function getSort(): array
    {
        $sort = [];
        foreach ($this->sort as $column => $dir) {
            if ($dir === null) {
                continue;
            }

            $field = str_replace('__', '.', $column);
            $sort[] = new SortObject($field, strtolower($dir));
        }

        return $sort;
    }

    protected function getFilters(): array
    {
        $filter = [];

        foreach ($this->search as $column => $value) {
            $field = str_replace('__', '.', $column);
            $filter[] = new FilterObject($field, 'like', "%$value%", 'and');
        }

        return $filter;
    }
}
