<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/specialties', 'specialty_')]
class SpecialtyController extends AbstractController
{
    #[Route('', 'list')]
    public function list(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/admin/pages/table.html.twig', [
            'title' => 'perscom.admin.specialties.list.title',
            'table' => 'PerscomSpecialtyTable'
        ]);
    }
}
