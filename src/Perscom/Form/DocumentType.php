<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Document;

class DocumentType extends AbstractPerscomEntityType
{
    protected function getEntityClass(): string
    {
        return Document::class;
    }
}
