<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomSyncResult;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomSyncResultRepository;
use Forumify\PerscomPlugin\Perscom\Sync\Message\SyncAllFromPerscomMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sync', 'sync')]
#[IsGranted('perscom-io.admin.sync')]
class SyncController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly PerscomSyncResultRepository $resultRepository,
    ) {
    }

    #[Route('', '')]
    public function __invoke(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/admin/sync/sync.html.twig');
    }

    #[Route('/initiate', '_initiate')]
    public function initialize(Request $request): Response
    {
        if (!$request->get('confirmed')) {
            return $this->render('@ForumifyPerscomPlugin/admin/sync/initiate_warning.html.twig');
        }

        $result = new PerscomSyncResult();
        $this->resultRepository->save($result);

        $this->messageBus->dispatch(new SyncAllFromPerscomMessage($result->getId()));

        $this->addFlash('success', 'Sync has been scheduled.');
        return $this->redirectToRoute('perscom_admin_sync');
    }
}
