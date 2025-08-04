<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller\Records;

use Forumify\PerscomPlugin\Perscom\Entity\Record\QualificationRecord;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/records/qualification', 'qualification_records')]
#[IsGranted('perscom-io.admin.records.qualification_records.view')]
class QualificationRecordController extends AbstractRecordCrudController
{
    protected ?string $permissionView = 'perscom-io.admin.records.qualification_records.view';
    protected ?string $permissionCreate = 'perscom-io.admin.records.qualification_records.create';
    protected ?string $permissionDelete = 'perscom-io.admin.records.qualification_records.delete';

    protected function getRecordType(): string
    {
        return 'qualification';
    }

    protected function getEntityClass(): string
    {
        return QualificationRecord::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomAdminQualificationRecordTable';
    }
}
