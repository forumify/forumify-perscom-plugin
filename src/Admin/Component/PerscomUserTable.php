<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\Core\Component\Table\AbstractTable;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Twig\Environment;

#[AsLiveComponent('PerscomUserTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.users.view')]
class PerscomUserTable extends AbstractDoctrineTable
{
    public function __construct(private readonly Environment $twig)
    {
        $this->sort = ['name' => AbstractTable::SORT_ASC];
    }

    protected function getEntityClass(): string
    {
        return PerscomUser::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('name', [
                'field' => 'name',
            ])
            ->addColumn('rank__name', [
                'field' => 'rank?.name',
                'label' => 'Rank',
            ])
            ->addColumn('position__name', [
                'field' => 'position?.name',
                'label' => 'Position',
            ])
            ->addColumn('unit__name', [
                'field' => 'unit?.name',
                'label' => 'Unit',
            ])
            ->addColumn('status__name', [
                'field' => 'status',
                'label' => 'Status',
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
}
