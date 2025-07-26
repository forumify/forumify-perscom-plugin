<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\Core\Repository\AbstractRepository;
use Forumify\PerscomPlugin\Perscom\Entity\FormField;

/**
 * @extends AbstractRepository<FormField>
 */
class FormFieldRepository extends AbstractRepository
{
    public static function getEntityClass(): string
    {
        return FormField::class;
    }
}
