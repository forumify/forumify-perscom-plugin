<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('perscom-io.admin.users.delete')]
class UserDeleteController extends AbstractController
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly PerscomUserRepository $perscomUserRepository,
    ) {
    }

    public function __invoke(Request $request, int $id): Response
    {
        $perscom = $this->perscomFactory->getPerscom();

        $user = $perscom->users()->get($id)->json('data');
        if (!$request->get('confirmed')) {
            return $this->render('@ForumifyPerscomPlugin/admin/users/delete.html.twig', [
                'user' => $user,
            ]);
        }

        $perscomUser = $this->perscomUserRepository->find($id);
        if ($perscomUser !== null) {
            $this->perscomUserRepository->remove($perscomUser);
        }

        $perscom->users()->delete($id);

        $this->addFlash('success', 'perscom.admin.users.delete.deleted');
        return $this->redirectToRoute('perscom_admin_user_list');
    }
}
