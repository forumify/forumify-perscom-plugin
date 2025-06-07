<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Contracts\ResourceContract;
use Perscom\Contracts\Searchable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractPerscomEntityType extends AbstractType
{
    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    abstract protected function getResource(Perscom $perscom): Searchable;

    public function configureOptions(OptionsResolver $resolver): void
    {
        $sorting = $this->getSorting();
        $filters = $this->getFilters();

        $perscom = $this->perscomFactory->getPerscom();
        $resource = $this->getResource($perscom);
        if ($resource instanceof ResourceContract && empty($sorting) && empty($filters)) {
            $entities = $resource
                ->all($this->getIncludes(), limit: 1000)
                ->json('data') ?? []
            ;
        } else {
            $entities = $resource
                ->search(
                    null,
                    $sorting,
                    $filters,
                    $this->getIncludes(),
                    limit: 1000
                )
                ->json('data') ?? []
            ;
        }

        $choiceLabelField = $this->getLabelField();
        $choiceIdField = $this->getIdField();

        $choices = [];
        foreach ($entities as $entity) {
            $choices[$entity[$choiceLabelField]] = $entity[$choiceIdField];
        }

        $resolver->setDefaults([
            'choices' => $choices,
            'placeholder' => ' ',
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

    protected function getFilters(): ?array
    {
        return null;
    }

    protected function getSorting(): ?array
    {
        return null;
    }

    protected function getIncludes(): array
    {
        return [];
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
