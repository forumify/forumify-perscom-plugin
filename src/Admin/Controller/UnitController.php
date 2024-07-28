<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/units', 'unit_')]
#[IsGranted('perscom-io.admin.organization.view')]
class UnitController extends AbstractController
{
    #[Route('', 'list')]
    public function list(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/admin/pages/table.html.twig', [
            'title' => 'perscom.admin.units.list.title',
            'table' => 'PerscomUnitTable'
        ]);
    }
}
