<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/positions', 'position_')]
#[IsGranted('perscom-io.admin.organization.view')]
class PositionController extends AbstractController
{
    #[Route('', 'list')]
    public function list(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/admin/pages/table.html.twig', [
            'title' => 'perscom.admin.positions.list.title',
            'table' => 'PerscomPositionTable'
        ]);
    }
}
