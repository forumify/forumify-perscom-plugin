<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\RosterType;
use Forumify\PerscomPlugin\Perscom\Entity\Roster;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rosters', 'roster')]
#[IsGranted('perscom-io.admin.organization.rosters.view')]
class RosterController extends AbstractCrudController
{
    protected string $listTemplate = '@ForumifyPerscomPlugin/admin/crud/list.html.twig';

    protected ?string $permissionView = 'perscom-io.admin.organization.rosters.view';
    protected ?string $permissionCreate = 'perscom-io.admin.organization.rosters.create';
    protected ?string $permissionEdit = 'perscom-io.admin.organization.rosters.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.organization.rosters.delete';

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return Roster::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomRosterTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(RosterType::class, $data);
    }
}
