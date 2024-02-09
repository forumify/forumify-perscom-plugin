<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RankType extends AbstractType
{
    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $ranks = $this->perscomFactory
            ->getPerscom()
            ->ranks()
            ->all(limit: 1000)
            ->json('data') ?? [];

        $choices = [];
        foreach ($ranks as $rank) {
            $choices[$rank['name']] = $rank['id'];
        }

        $resolver->setDefaults([
            'choices' => $choices,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
