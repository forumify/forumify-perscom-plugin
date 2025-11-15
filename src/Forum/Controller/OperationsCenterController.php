<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Repository\FormRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OperationsCenterController extends AbstractController
{
    public function __construct(
        private readonly PerscomUserService $perscomUserService,
        private readonly SettingRepository $settingRepository,
        private readonly FormRepository $formRepository,
    ) {
    }

    #[Route('/operations-center', 'operations_center')]
    public function __invoke(): Response
    {
        $perscomUser = $this->perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            throw $this->createNotFoundException('Your PERSCOM user was not found');
        }

        $announcement = $this->settingRepository->get('perscom.opcenter.announcement');
        if (trim(strip_tags($announcement ?? '', ['img'])) === '') {
            $announcement = null;
        }

        return $this->render('@ForumifyPerscomPlugin/frontend/operations_center/operations_center.html.twig', [
            'announcement' => $announcement,
            'forms' => $this->formRepository->findAllSubmissionsAllowed(),
            'user' => $perscomUser,
        ]);
    }
}
