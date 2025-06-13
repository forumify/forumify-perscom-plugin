<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Doctrine\ORM\QueryBuilder;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('AssignmentRecordTable', '@ForumifyPerscomPlugin/frontend/components/record_table.html.twig')]
class AssignmentRecordTable extends AbstractRecordTable
{
    protected array $searchFields = ['position.name', 'unit.name'];

    protected function getEntityClass(): string
    {
        return AssignmentRecord::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addDateColumn()
            ->addColumn('unit', [
                'field' => 'unit.name',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
            ])
            ->addColumn('position', [
                'field' => 'position.name',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
            ]);
    }

    protected function getQuery(array $search): QueryBuilder
    {
        $qb = parent::getQuery($search);
        return $qb->andWhere($qb->expr()->orX('e.position IS NOT NULL', 'e.unit IS NOT NULL'));
    }
}
