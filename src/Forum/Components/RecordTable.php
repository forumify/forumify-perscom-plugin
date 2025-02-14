<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Core\Component\Table\AbstractTable;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Twig\Environment;

#[AsLiveComponent('RecordTable', '@Forumify/components/table/table.html.twig')]
class RecordTable extends AbstractTable
{
    #[LiveProp]
    public array $data;
    protected TranslatorInterface $translator;
    protected Environment $twig;

    #[Required]
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    #[Required]
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
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
            ])
            ->addDocumentColumn();
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

    protected function addDocumentColumn(bool $recordTextModal = false, ?string $itemKey = null): static
    {
        $this->addColumn('document', [
            'sortable' => false,
            'searchable' => false,
            'label' => '',
            'renderer' => fn ($_, array $record) => $this->twig->render('@ForumifyPerscomPlugin/frontend/components/record_table/documents.html.twig', [
                'record' => $record,
                'showRecordText' => $recordTextModal,
                'item' => $itemKey !== null
                    ? ($record[$itemKey] ?? null)
                    : null,
            ]),
        ]);

        return $this;
    }

    protected function modifyData(): void
    {
    }

    protected function getData(int $limit, int $offset, array $search, array $sort): array
    {
        $this->modifyData();
        return array_slice($this->data, $offset, $limit);
    }

    protected function getTotalCount(array $search): int
    {
        return count($this->data);
    }
}
