<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AwardType extends AbstractType
{
    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $awards = $this->perscomFactory
            ->getPerscom()
            ->awards()
            ->all(limit: 1000)
            ->json('data') ?? [];

        $choices = [];
        foreach ($awards as $award) {
            $choices[$award['name']] = $award['id'];
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
