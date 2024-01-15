<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Core\Component\Table\AbstractTable;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('RecordTable', '@Forumify/components/table/table.html.twig')]
class RecordTable extends AbstractTable
{
    #[LiveProp]
    public array $data;

    public function __construct(protected readonly TranslatorInterface $translator)
    {
    }

    protected function buildTable(): void
    {
        $this
            ->addDateColumn()
            ->addColumn('text', [
                'field' => '[text]',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
            ]);
    }

    protected function addDateColumn(): static
    {
        $this->addColumn('date', [
            'field' => '[created_at]',
            'sortable' => false,
            'searchable' => false,
            'class' => 'w-25 text-small',
            'renderer' => fn (string $date) => $this->translator->trans('date', ['date' => new \DateTime($date)]),
        ]);

        return $this;
    }

    protected function getData(int $limit, int $offset, array $search, array $sort): array
    {
        return array_slice($this->data, $offset, $limit);
    }

    protected function getTotalCount(array $search): int
    {
        return count($this->data);
    }
}
