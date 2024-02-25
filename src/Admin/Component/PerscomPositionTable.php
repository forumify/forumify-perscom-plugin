<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomPositionTable', '@Forumify/components/table/table.html.twig')]
class PerscomPositionTable extends AbstractPerscomTable
{
    protected function buildTable(): void
    {
        $this
            ->addColumn('name', [
                'field' => '[name]'
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
        return 'positions';
    }
}
