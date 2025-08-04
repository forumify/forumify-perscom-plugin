<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Forumify\PerscomPlugin\Perscom\Entity\Form;

/**
 * @extends AbstractPerscomRepository<Form>
 */
class FormRepository extends AbstractPerscomRepository
{
    public static function getEntityClass(): string
    {
        return Form::class;
    }
}
