<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Form;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Entity\SortableEntityInterface;
use Forumify\Core\Repository\AbstractRepository;
use LogicException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractPerscomEntityType extends AbstractType
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

    abstract protected function getEntityClass(): string;

    public function configureOptions(OptionsResolver $resolver): void
    {
        $data = $this
            ->getQueryBuilder()
            ->getQuery()
            ->getArrayResult()
        ;

        $choices = [];
        foreach ($data as $row) {
            $choices[$row['label']] = $row['key'];
        }

        $resolver->setDefaults([
            'choices' => $choices,
            'placeholder' => ' ',
        ]);
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        $er = $this->em->getRepository($this->getEntityClass());
        if (!$er instanceof AbstractRepository) {
            throw new LogicException("Repository for {$this->getEntityClass()} must extend AbstractRepository");
        }

        $qb = $er->createQueryBuilder('e')->select(
            "e.{$this->getIdField()} AS key",
            "e.{$this->getLabelField()} AS label",
        );

        if (is_a($this->getEntityClass(), SortableEntityInterface::class, true)) {
            $qb->orderBy('e.position', 'ASC');
        }

        return $qb;
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
