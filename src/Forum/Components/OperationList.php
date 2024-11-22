<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Doctrine\ORM\QueryBuilder;
use Forumify\Core\Component\List\AbstractDoctrineList;
use Forumify\PerscomPlugin\Perscom\Repository\OperationRepository;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('Perscom\\OperationList', '@ForumifyPerscomPlugin/frontend/components/operation_list.html.twig')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class OperationList extends AbstractDoctrineList
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
    ) {
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->operationRepository->createListQueryBuilder();
    }

    protected function getCount(): int
    {
        return $this->operationRepository->createListQueryBuilder()
            ->select('COUNT(o)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
