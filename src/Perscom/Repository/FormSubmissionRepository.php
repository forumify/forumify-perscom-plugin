<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\FormSubmission;

class FormSubmissionRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return FormSubmission::class;
    }
}
