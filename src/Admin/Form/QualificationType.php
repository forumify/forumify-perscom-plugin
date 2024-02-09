<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QualificationType extends AbstractType
{
    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $qualifications = $this->perscomFactory
            ->getPerscom()
            ->qualifications()
            ->all(limit: 1000)
            ->json('data') ?? [];

        $choices = [];
        foreach ($qualifications as $qualification) {
            $choices[$qualification['name']] = $qualification['id'];
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
