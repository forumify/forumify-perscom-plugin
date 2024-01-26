<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class OperationsCenterController extends AbstractController
{
    #[Route('/operations-center', 'operations_center')]
    public function __invoke(
        PerscomUserService $perscomUserService,
        PerscomFactory $perscomFactory,
    ): Response {
        $perscomUser = $perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            throw new NotFoundHttpException('Your PERSCOM user was not found');
        }

        $perscom = $perscomFactory->getPerscom();
        $perscomUser = $perscom->users()
            ->get($perscomUser['id'], ['status', 'rank', 'position'])
            ->json('data');

        $announcements = $perscom->announcements()
            ->search([
                'filters' => [
                    [
                        'field' => 'expires_at',
                        'operator' => '>=',
                        'value' => (new \DateTime())->format(Perscom::DATE_FORMAT),
                    ],
                    [
                        'type' => 'or',
                        'field' => 'expires_at',
                        'operator' => '=',
                        'value' => null,
                    ],
                ],
            ])
            ->json('data');

        $forms = $perscom->forms()
            ->search([
                'filters' => [
                    [
                        'field' => 'id',
                        'operator' => '!=',
                        'value' => 1,
                    ],
                ],
            ])
            ->json('data');

        return $this->render('@ForumifyPerscomPlugin/frontend/operations_center/operations_center.html.twig', [
            'user' => $perscomUser,
            'announcements' => $announcements,
            'newsfeed' => [],
            'forms' => $forms,
        ]);
    }
}
