<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Perscom\Service\SyncService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SyncController extends AbstractController
{
    #[Route('/sync', 'sync')]
    public function __invoke(Request $request, SyncService $syncService): Response
    {
        if (!$request->get('confirmed')) {
            return $this->render('@ForumifyPerscomPlugin/admin/sync.html.twig');
        }

        $syncService->sync();

        return $this->redirectToRoute('perscom_admin_settings');
    }
}
