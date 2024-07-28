<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Twig\Environment;

#[AsLiveComponent('PerscomStatusTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.organization.view')]
class PerscomStatusTable extends AbstractPerscomTable
{
    public function __construct(private readonly Environment $twig)
    {
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('name', [
                'field' => '[name]',
            ])
            ->addColumn('appearance', [
                'sortable' => false,
                'searchable' => false,
                'renderer' => fn ($_, $status) => $this->twig->render('@ForumifyPerscomPlugin/frontend/roster/components/status.html.twig', ['status' => $status])
            ])
            ->addColumn('actions', [
                'label' => '',
                'renderer' => fn () => '',
                'searchable' => false,
                'sortable' => false,
            ]);
    }

    protected function getResource(): string
    {
        return 'statuses';
    }
}
