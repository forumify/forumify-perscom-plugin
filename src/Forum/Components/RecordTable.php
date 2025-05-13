<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Core\Component\Table\AbstractTable;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Twig\Environment;

#[AsLiveComponent('RecordTable', '@ForumifyPerscomPlugin/frontend/components/record_table.html.twig')]
class RecordTable extends AbstractTable
{
    #[LiveProp]
    public array $data;
    private ?array $filteredData = null;

    #[LiveProp(writable: true)]
    public string $query = '';

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

    protected function filterData(array $fields): callable
    {
        $query = strtolower(trim($this->query));
        $propertyAccessor = new PropertyAccessor();
        return function (array $record) use ($fields, $query, $propertyAccessor): bool {
            if (empty($query)) {
                return true;
            }

            $searchBody = '';
            foreach ($fields as $field) {
                $searchBody .= $propertyAccessor->getValue($record, $field);
            }
            return str_contains(strtolower($searchBody), $query);
        };
    }


    private function getDataInner(): array
    {
        if ($this->filteredData !== null) {
            return $this->filteredData;
        }

        $this->filteredData = array_filter($this->data, $this->filterData(['[text]']));
        return $this->filteredData;
    }

    protected function getData(int $limit, int $offset, array $search, array $sort): array
    {
        return array_slice($this->getDataInner(), $offset, $limit);
    }

    protected function getTotalCount(array $search): int
    {
        return count($this->getDataInner());
    }
}
