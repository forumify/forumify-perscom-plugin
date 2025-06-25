<?php

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\RankType;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ranks', 'rank')]
#[IsGranted('perscom-io.admin.organization.view')]
class RankController extends AbstractCrudController
{
    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Rank::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomRankTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(RankType::class, $data);
    }
}
