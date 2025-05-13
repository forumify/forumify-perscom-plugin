<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Perscom;
use Perscom\Contracts\Searchable;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractPerscomEntityType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
    }

    protected function getResource(Perscom $perscom): Searchable
    {
        return $perscom->users();
    }
}
