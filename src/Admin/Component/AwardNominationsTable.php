<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use DateTime;
use Forumify\Core\Component\Table\AbstractTable;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\AwardNominationRepository;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Twig\Environment;

#[AsLiveComponent('AwardNominationsTable', '@ForumifyPerscomPlugin/frontend/components/award_nominations_table.html.twig')]
class AwardNominationsTable extends AbstractTable
{
    private ?array $data = null;

    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly AwardNominationRepository $awardNominationRepository,
        private readonly TranslatorInterface $translator,
        private readonly Environment $twig
    ) {
    }

    protected function buildTable(): void
    {
        $this
             ->addColumn('date', [
                 'field' => 'nomination.createdDate',
                 'sortable' => false,
                 'searchable' => false,
                 'class' => 'w-10',
                 'renderer' => fn (DateTime $date) => $this->translator->trans('date', ['date' => $date]),
             ])
            ->addColumn('receiver', [
                'field' => 'receiverItem[name]',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left',
            ])
            ->addColumn('award', [
                'field' => 'awardItem[name]',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left',
            ])
            ->addColumn('status', [
                'field' => 'statusItem',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left',
                'renderer' => fn ($status) => $status !== null
                    ? $this->twig->render('@ForumifyPerscomPlugin/frontend/roster/components/status.html.twig', [
                        'class' => 'text-small',
                        'status' => $status
                    ])
                    : '',
            ])
            ->addColumn('actions', [
                'label' => '',
                'renderer' => fn ($_, $row) => $this->twig->render('@ForumifyPerscomPlugin/admin/award_nominations/actions.html.twig', [
                    'data' => $row,
                ]),
                'searchable' => false,
                'sortable' => false,
            ]);
    }

    protected function getData(int $limit, int $offset, array $search, array $sort): array
    {
        $qb = $this->awardNominationRepository->getListQuery();

        $this->data = $this->awardNominationRepository->getAwardNominations($qb);
        return array_slice($this->data, $offset, $limit);
    }

    protected function getTotalCount(array $search): int
    {
        return count($this->data);
    }
}
