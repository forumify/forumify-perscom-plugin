<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractTable;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Twig\Environment;

#[AsLiveComponent('PerscomUserTable', '@Forumify/components/table/table.html.twig')]
class PerscomUserTable extends AbstractTable
{
    private ?array $perscomResult = null;

    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly Environment $twig,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('name', [
                'field' => '[name]',
                'searchable' => false,
                'sortable' => false,
            ])
            ->addColumn('rank', [
                'field' => '[rank?][name]',
                'searchable' => false,
                'sortable' => false,
            ])
            ->addColumn('position', [
                'field' => '[position?][name]',
                'searchable' => false,
                'sortable' => false,
            ])
            ->addColumn('unit', [
                'field' => '[unit?][name]',
                'searchable' => false,
                'sortable' => false,
            ])
            ->addColumn('status', [
                'field' => '[status]',
                'searchable' => false,
                'sortable' => false,
                'renderer' => fn ($status) => $status !== null
                    ? $this->twig->render('@ForumifyPerscomPlugin/frontend/roster/components/status.html.twig', ['status' => $status])
                    : '',
            ])
            ->addColumn('actions', [
                'label' => '',
                'renderer' => fn ($_, $row) => $this->twig->render('@ForumifyPerscomPlugin/admin/users/list/actions.html.twig', ['row' => $row]),
                'searchable' => false,
                'sortable' => false,
            ]);
    }

    protected function getData(int $limit, int $offset, array $search, array $sort): array
    {
        return $this->getPerscomData()['data'] ?? [];
    }

    protected function getTotalCount(array $search): int
    {
        return $this->getPerscomData()['meta']['total'] ?? 0;
    }

    private function getPerscomData(): array
    {
        if ($this->perscomResult !== null) {
            return $this->perscomResult;
        }

        $this->perscomResult = $this->perscomFactory
            ->getPerscom()
            ->users()
            ->all(['rank', 'position', 'unit', 'status'], $this->page, $this->limit)
            ->json();

        return $this->perscomResult;
    }
}
