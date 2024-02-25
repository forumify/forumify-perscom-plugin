<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomSpecialtyTable', '@Forumify/components/table/table.html.twig')]
class PerscomSpecialtyTable extends AbstractPerscomTable
{
    protected function buildTable(): void
    {
        $this
            ->addColumn('abbreviation', [
                'field' => '[abbreviation]'
            ])
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
        return 'specialties';
    }
}
