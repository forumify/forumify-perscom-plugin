<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use DateTimeInterface;
use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomSyncResult;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Twig\Environment;

#[AsLiveComponent('PerscomSyncResultTable', '@Forumify/components/table/table.html.twig')]
class PerscomSyncResultTable extends AbstractDoctrineTable
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly Environment $twig,
    ) {
        $this->sort = ['start' => self::SORT_DESC];
        $this->limit = 5;
    }

    protected function getEntityClass(): string
    {
        return PerscomSyncResult::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('start',[
                'field' => 'start',
                'label' => 'Started at',
                'searchable' => false,
                'renderer' => fn (DateTimeInterface $date) => $this->translator->trans('date_time_short', ['date' => $date]),
            ])
            ->addColumn('end', [
                'field' => 'end',
                'label' => 'Completed at',
                'searchable' => false,
                'renderer' => fn (?DateTimeInterface $date) => $date !== null
                    ? $this->translator->trans('date_time_short', ['date' => $date])
                    : '',
            ])
            ->addColumn('success', [
                'field' => 'success',
                'label' => 'Result',
                'searchable' => false,
                'sortable' => false,
                'renderer' => $this->renderSuccess(...),
            ])
            ->addColumn('logs', [
                'field' => 'errorMessage',
                'label' => '',
                'searchable' => false,
                'sortable' => false,
                'renderer' => $this->renderLogs(...),
            ])
        ;
    }

    private function renderSuccess(?bool $success): string
    {
        $backgroundColor = $success === null ? 'blue' : ($success === true ? 'green' : 'red');
        $text = $success === null ? 'Running' : ($success === true ? 'Completed' : 'Error');

        return "<span class=\"pl-2 pr-2 pt-1 pb-1 text-black text-small\" style=\"
            border-radius: 100px;
            color: white;
            background-color: $backgroundColor;
        \">
            $text
        </span>";
    }

    private function renderLogs(string $errorMessages): string
    {
        return $this->twig->render('@ForumifyPerscomPlugin/admin/sync/sync_table_logs.html.twig', [
            'errorMessages' => $errorMessages,
        ]);
    }
}
