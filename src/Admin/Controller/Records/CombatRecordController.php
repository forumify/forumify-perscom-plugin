<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller\Records;

use Forumify\PerscomPlugin\Perscom\Entity\Record\CombatRecord;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/records/combat', 'combat_records')]
#[IsGranted('perscom-io.admin.records.combat_records.view')]
class CombatRecordController extends AbstractRecordCrudController
{
    protected ?string $permissionView = 'perscom-io.admin.records.combat_records.view';
    protected ?string $permissionCreate = 'perscom-io.admin.records.combat_records.create';
    protected ?string $permissionDelete = 'perscom-io.admin.records.combat_records.delete';

    protected function getRecordType(): string
    {
        return 'combat';
    }

    protected function getEntityClass(): string
    {
        return CombatRecord::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomAdminCombatRecordTable';
    }
}
