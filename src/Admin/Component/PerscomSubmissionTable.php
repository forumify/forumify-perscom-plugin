<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Forumify\Core\Component\Table\AbstractTable;
use Forumify\PerscomPlugin\Perscom\Entity\FormSubmission;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Twig\Environment;

#[AsLiveComponent('PerscomSubmissionTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.submissions.view')]
class PerscomSubmissionTable extends AbstractDoctrineTable
{
    #[LiveProp]
    public ?int $form = null;

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly Environment $twig,
    ) {
        $this->sort = ['createdAt' => AbstractTable::SORT_DESC];
    }

    protected function getEntityClass(): string
    {
        return FormSubmission::class;
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('user__name', [
                'field' => 'user?.name',
                'label' => 'Name',
            ])
            ->addColumn('form__name', [
                'field' => 'form?.name',
                'label' => 'Form',
            ])
            ->addColumn('status', [
                'field' => 'status',
                'searchable' => false,
                'sortable' => false,
                'renderer' => fn ($status) => $status !== null
                    ? $this->twig->render('@ForumifyPerscomPlugin/frontend/roster/components/status.html.twig', [
                        'class' => 'text-small',
                        'status' => $status
                    ])
                    : '',
            ])
            ->addColumn('created_at', [
                'field' => 'createdAt',
                'label' => 'Created At',
                'searchable' => false,
                'renderer' => fn (?DateTime $date) => $this->translator->trans('date_time_short', ['date' => $date]),
            ])
            ->addColumn('actions', [
                'field' => 'id',
                'label' => '',
                'renderer' => $this->renderActions(...),
                'searchable' => false,
                'sortable' => false,
            ]);
    }

    private function renderActions(int $id): string
    {
        return $this->renderAction('perscom_admin_submission_view', ['id' => $id], 'eye');
    }

    protected function getQuery(array $search): QueryBuilder
    {
        $qb = parent::getQuery($search);
        if ($this->form !== null) {
            $qb->andWhere('e.form = :form')
                ->setParameter('form', $this->form)
            ;
        }

        return $qb;
    }
}
