<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use DateTime;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Perscom\Data\FilterObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OperationsCenterController extends AbstractController
{
    #[Route('/operations-center', 'operations_center')]
    public function __invoke(
        PerscomUserService $perscomUserService,
        PerscomFactory $perscomFactory,
        SettingRepository $settingRepository,
    ): Response {
        $perscomUser = $perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            throw $this->createNotFoundException('Your PERSCOM user was not found');
        }

        $perscom = $perscomFactory->getPerscom();
        $perscomUser = $perscom->users()
            ->get($perscomUser['id'], ['status', 'rank', 'position'])
            ->json('data');

        $announcements = $perscom->announcements()
            ->search(filter: [
                new FilterObject('expires_at', '>=', (new DateTime())->format(Perscom::DATE_FORMAT)),
                new FilterObject('expires_at', '=', null)
            ])
            ->json('data');

        $enlistmentForm = $settingRepository->get('perscom.enlistment.form');
        $filters = $enlistmentForm === null ? [] : [
            new FilterObject('id', '!=', $enlistmentForm)
        ];

        $forms = $perscom->forms()
            ->search(filter: $filters)
            ->json('data');

        return $this->render('@ForumifyPerscomPlugin/frontend/operations_center/operations_center.html.twig', [
            'user' => $perscomUser,
            'announcements' => $announcements,
            'newsfeed' => [],
            'forms' => $forms,
        ]);
    }
}
