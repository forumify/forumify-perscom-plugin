<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use DateTime;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\FormRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Perscom\Data\FilterObject;
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
    public function __invoke(
        PerscomFactory $perscomFactory,
    ): Response {
        $perscomUser = $this->perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            throw $this->createNotFoundException('Your PERSCOM user was not found');
        }

        // TODO: announcements
        $perscom = $perscomFactory->getPerscom();
        $announcements = $perscom->announcements()
            ->search(filter: [
                new FilterObject('expires_at', '>=', (new DateTime())->format(Perscom::DATE_FORMAT)),
                new FilterObject('expires_at', '=', null)
            ])
            ->json('data');

        $forms = $this->formRepository->findAll();
        $enlistmentFormId = $this->settingRepository->get('perscom.enlistment.form');
        if ($enlistmentFormId !== null) {
            $forms = array_filter($forms, fn (Form $form) => $form->getId() !== $enlistmentFormId);
        }

        return $this->render('@ForumifyPerscomPlugin/frontend/operations_center/operations_center.html.twig', [
            'user' => $perscomUser,
            'announcements' => $announcements,
            'forms' => $forms,
        ]);
    }
}
