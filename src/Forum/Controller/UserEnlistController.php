<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Entity\User;
use Forumify\PerscomPlugin\Forum\Form\Enlistment;
use Forumify\PerscomPlugin\Forum\Form\EnlistmentType;
use Forumify\PerscomPlugin\Perscom\Entity\EnlistmentTopic;
use Forumify\PerscomPlugin\Perscom\Repository\EnlistmentTopicRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomEnlistService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserEnlistController extends AbstractController
{
    #[Route('/enlist', 'enlist')]
    public function __invoke(
        PerscomEnlistService $perscomEnlistService,
        EnlistmentTopicRepository $enlistmentTopicRepository,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!$perscomEnlistService->canEnlist()) {
            $this->addFlash('error', 'perscom.enlistment.not_eligible');
            return $this->redirectToRoute('forumify_core_index');
        }

        /** @var User $user */
        $user = $this->getUser();
        $enlistmentForm = $perscomEnlistService->getEnlistmentForm();
        if ($enlistmentForm === null) {
            $this->addFlash('error', 'perscom.enlistment.not_enabled');
            return $this->redirectToRoute('forumify_core_index');
        }

        /** @var array<EnlistmentTopic> $enlistmentTopics */
        $enlistmentTopics = $enlistmentTopicRepository->findBy(['user' => $user], ['submissionId' => 'DESC'], 1);
        $enlistmentTopic = reset($enlistmentTopics);
        if ($enlistmentTopic !== false && $request->get('force_new') === null) {
            $submission = $perscomEnlistService->getCurrentEnlistment($enlistmentTopic->getSubmissionId());
            if ($submission !== null) {
                return $this->render('@ForumifyPerscomPlugin/frontend/enlistment/enlist_success.html.twig', [
                    'enlistmentTopic' => $enlistmentTopic,
                    'successMessage' => $enlistmentForm->getSuccessMessage(),
                ]);
            }
            // The submission was deleted from PERSCOM.io, continue with the enlistment
        }

        $enlistment = new Enlistment();
        $enlistment->email = $user->getEmail();

        $form = $this->createForm(EnlistmentType::class, $enlistment, ['form' => $enlistmentForm]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $enlistmentTopic = $perscomEnlistService->enlist($form->getData());

            return $this->render('@ForumifyPerscomPlugin/frontend/enlistment/enlist_success.html.twig', [
                'enlistmentTopic' => $enlistmentTopic,
                'successMessage' => $enlistmentForm->getSuccessMessage(),
            ]);
        }

        return $this->render('@ForumifyPerscomPlugin/frontend/enlistment/enlist.html.twig', [
            'form' => $form->createView(),
            'instructions' => $enlistmentForm->getInstructions(),
        ]);
    }
}
