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
    private PerscomFactory $perscomFactory;
    private AwardNominationRepository $awardNominationRepository;
    private ?array $data = null;

    #[Required]
    public function setPerscomFactory(PerscomFactory $perscomFactory): void
    {
        $this->perscomFactory = $perscomFactory;
    }

    #[Required]
    public function setAwardNominationRepository(AwardNominationRepository $awardNominationRepository): void
    {
        $this->awardNominationRepository = $awardNominationRepository;
    }

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
             ->addColumn('date', [
                 'field' => 'nomination.createdDate',
                 'sortable' => false,
                 'searchable' => false,
                 'class' => 'w-10 text-small',
                 'renderer' => fn (DateTime $date) => $this->translator->trans('date', ['date' => $date]),
             ])
            ->addColumn('requester', [
                'field' => 'requesterItem[name]',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small'
            ])
            ->addColumn('receiver', [
                'field' => 'receiverItem[name]',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
            ])
            ->addColumn('award', [
                'field' => 'awardItem[name]',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
            ])
            ->addColumn('reason', [
                'field' => 'nomination.reason',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
            ])
            ->addColumn('status', [
                'field' => 'statusItem',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
                'renderer' => fn ($status) => $status !== null
                    ? $this->twig->render('@ForumifyPerscomPlugin/frontend/roster/components/status.html.twig', [
                        'class' => 'text-small',
                        'status' => $status
                    ])
                    : '',
            ])
            ->addColumn('reviewed by', [
                'field' => 'updatedByItem?[name]',
                'sortable' => false,
                'searchable' => false,
                'class' => 'text-left text-small',
            ])
            ->addColumn('actions', [
                'label' => '',
                'renderer' => fn ($_, $row) => $this->twig->render('@ForumifyPerscomPlugin/admin/awardnominations/actions.html.twig', [
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
