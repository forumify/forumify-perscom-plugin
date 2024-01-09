<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Saloon\Exceptions\Request\Statuses\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    #[Route('user/{id<\d+>}', 'user')]
    public function __invoke(
        int $id,
        PerscomFactory $perscomFactory,
        UserRepository $userRepository,
        Request $request,
        TranslatorInterface $translator,
    ): Response {
        try {
            $user = $perscomFactory->getPerscom()
                ->users()
                ->get($id, ['rank', 'status', 'position', 'unit', 'profile'])
                ->json('data');
        } catch (NotFoundException) {
            throw new NotFoundHttpException($translator->trans('perscom.user.not_found'));
        }

        $forumUser = $userRepository->findOneBy(['email' => $user['email']]);

        return $this->render('@ForumifyPerscomPlugin/frontend/user/user.html.twig', [
            'user' => $user,
            'forumAccount' => $forumUser,
        ]);
    }
}
