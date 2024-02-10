<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Contracts\ResourceContract;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractPerscomEntityType extends AbstractType
{
    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    abstract protected function getResource(Perscom $perscom): ResourceContract;

    public function configureOptions(OptionsResolver $resolver): void
    {
        $perscom = $this->perscomFactory->getPerscom();
        $entities = $this->getResource($perscom)
            ->all(limit: 1000)
            ->json('data') ?? [];

        $choiceLabelField = $this->getLabelField();
        $choiceIdField = $this->getIdField();

        $choices = [];
        foreach ($entities as $entity) {
            $choices[$entity[$choiceLabelField]] = $entity[$choiceIdField];
        }

        $resolver->setDefaults([
            'choices' => $choices,
        ]);
    }

    protected function getLabelField(): string
    {
        return 'name';
    }

    protected function getIdField(): string
    {
        return 'id';
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
