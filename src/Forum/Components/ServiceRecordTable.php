<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('ServiceRecordTable', '@ForumifyPerscomPlugin/frontend/components/record_table.html.twig')]
class ServiceRecordTable extends AbstractRecordTable
{
    protected function getEntityClass(): string
    {
        return ServiceRecord::class;
    }
}
