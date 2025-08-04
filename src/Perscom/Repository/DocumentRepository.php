<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Document;

class DocumentRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return Document::class;
    }
}
