<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PerscomUserController extends AbstractController
{
    #[Route('user/{id<\d+>}', 'user')]
    public function __invoke(int $id, PerscomFactory $perscomFactory, UserRepository $userRepository, Request $request): Response
    {
        $perscomUser = $perscomFactory->getPerscom()
            ->users()
            ->get($id)
            ->json('data');

        if ($perscomUser === null) {
            return $this->userNotFound($request);
        }

        $user = $userRepository->findOneBy(['email' => $perscomUser['email']]);
        if ($user === null) {
            return $this->userNotFound($request);
        }

        return $this->redirectToRoute('forumify_forum_profile', [
            'username' => $user->getUsername()
        ], Response::HTTP_MOVED_PERMANENTLY);
    }

    private function userNotFound(Request $request): Response
    {
        $this->addFlash('error', 'perscom.roster.user_missing');

        if ($referer = $request->headers->get('referer')) {
            $this->redirect($referer);
        }
        return $this->redirectToRoute('perscom_roster');
    }
}
