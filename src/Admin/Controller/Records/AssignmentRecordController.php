<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller\Records;

use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/records/assignment', 'assignment_records')]
#[IsGranted('perscom-io.admin.records.assignment_records.view')]
class AssignmentRecordController extends AbstractRecordCrudController
{
    protected ?string $permissionView = 'perscom-io.admin.records.assignment_records.view';
    protected ?string $permissionCreate = 'perscom-io.admin.records.assignment_records.create';
    protected ?string $permissionDelete = 'perscom-io.admin.records.assignment_records.delete';

    protected function getRecordType(): string
    {
        return 'assignment';
    }

    protected function getEntityClass(): string
    {
        return AssignmentRecord::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomAdminAssignmentRecordTable';
    }
}
