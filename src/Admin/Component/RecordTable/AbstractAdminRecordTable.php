<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component\RecordTable;

use DateTimeInterface;
use Forumify\Core\Component\Table\AbstractDoctrineTable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractAdminRecordTable extends AbstractDoctrineTable
{
    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected readonly UrlGeneratorInterface $urlGenerator,
        private readonly Security $security,
    ) {
        $this->sort = ['createdAt' => self::SORT_DESC];
    }

    abstract protected function addRecordColumns(): static;

    abstract protected function getRecordType(): string;

    protected function buildTable(): void
    {
        $this
            ->addDateColumn()
            ->addColumn('user', [
                'field' => 'user.name',
                // TODO: https://github.com/forumify/forumify-platform/issues/115
                'searchable' => false,
                'sortable' => false,
            ])
            ->addRecordColumns()
            ->addColumn('actions', [
                'field' => 'id',
                'label' => '',
                'renderer' => $this->renderActions(...),
                'searchable' => false,
                'sortable' => false,
            ])
        ;
    }

    protected function addDateColumn(): static
    {
        $this->addColumn('createdAt', [
            'field' => 'createdAt',
            'label' => 'Date',
            'renderer' => fn(DateTimeInterface $date) => $this->translator->trans('date_time_short', ['date' => $date]),
            'searchable' => false,
        ]);

        return $this;
    }

    private function renderActions(int $id): string
    {
        $recordType = $this->getRecordType();

        return $this->security->isGranted("perscom-io.admin.records.$recordType.delete")
            ? $this->renderAction("perscom_admin_{$recordType}_delete", ['identifier' => $id], 'x')
            : '';
    }
}
