<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller\Records;

use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/records/rank', 'rank_records')]
#[IsGranted('perscom-io.admin.records.rank_records.view')]
class RankRecordController extends AbstractRecordCrudController
{
    protected ?string $permissionView = 'perscom-io.admin.records.rank_records.view';
    protected ?string $permissionCreate = 'perscom-io.admin.records.rank_records.create';
    protected ?string $permissionDelete = 'perscom-io.admin.records.rank_records.delete';

    protected function getRecordType(): string
    {
        return 'rank';
    }

    protected function getEntityClass(): string
    {
        return RankRecord::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomAdminRankRecordTable';
    }
}
