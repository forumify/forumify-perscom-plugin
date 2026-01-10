<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Entity\User;
use Forumify\PerscomPlugin\Forum\Form\Enlistment;
use Forumify\PerscomPlugin\Forum\Form\EnlistmentType;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Service\PerscomEnlistService;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserEnlistController extends AbstractController
{
    public function __construct(
        private readonly PerscomEnlistService $perscomEnlistService,
        private readonly PerscomUserService $perscomUserService,
    ) {
    }

    #[Route('/enlist', 'enlist')]
    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();
        if ($user->isBanned()) {
            throw $this->createAccessDeniedException();
        }

        if (!$user->isEmailVerified()) {
            $this->addFlash('error', 'perscom.enlistment.not_verified');
            return $this->redirectToRoute('forumify_core_index');
        }

        if (!$this->perscomEnlistService->canEnlist()) {
            $this->addFlash('error', 'perscom.enlistment.not_eligible');
            return $this->redirectToRoute('forumify_core_index');
        }

        $enlistmentForm = $this->perscomEnlistService->getEnlistmentForm();
        if ($enlistmentForm === null) {
            $this->addFlash('error', 'perscom.enlistment.not_enabled');
            return $this->redirectToRoute('forumify_core_index');
        }

        /** @var PerscomUser|null $perscomUser */
        $perscomUser = $this->perscomUserService->getPerscomUser($user);
        if ($perscomUser !== null && !$request->query->get('force_new')) {
            return $this->render('@ForumifyPerscomPlugin/frontend/enlistment/enlist_success.html.twig', [
                'enlistmentTopic' => $perscomUser->getEnlistmentTopic(),
                'successMessage' => $enlistmentForm->getSuccessMessage(),
            ]);
        }

        $enlistment = new Enlistment();
        $enlistment->email = $user->getEmail();

        $form = $this->createForm(EnlistmentType::class, $enlistment, ['form' => $enlistmentForm]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $perscomUser = $this->perscomEnlistService->enlist($form->getData());

            return $this->render('@ForumifyPerscomPlugin/frontend/enlistment/enlist_success.html.twig', [
                'enlistmentTopic' => $perscomUser->getEnlistmentTopic(),
                'successMessage' => $enlistmentForm->getSuccessMessage(),
            ]);
        }

        return $this->render('@ForumifyPerscomPlugin/frontend/enlistment/enlist.html.twig', [
            'form' => $form->createView(),
            'instructions' => $enlistmentForm->getInstructions(),
        ]);
    }
}
