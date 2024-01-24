<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Entity\User;
use Forumify\PerscomPlugin\Forum\Form\Enlistment;
use Forumify\PerscomPlugin\Forum\Form\EnlistmentType;
use Forumify\PerscomPlugin\Perscom\Entity\EnlistmentTopic;
use Forumify\PerscomPlugin\Perscom\Repository\EnlistmentTopicRepository;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\PerscomEnlistService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UserEnlistController extends AbstractController
{
    #[Route('/enlist', 'enlist')]
    public function __invoke(
        PerscomEnlistService $perscomEnlistService,
        PerscomFactory $perscomFactory,
        EnlistmentTopicRepository $enlistmentTopicRepository,
        Request $request,
    ) {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!$perscomEnlistService->canEnlist()) {
            $this->addFlash('error', 'perscom.enlistment.not_eligible');
            return $this->redirectToRoute('forumify_core_index');
        }

        /** @var User $user */
        $user = $this->getUser();
        $enlistmentForm = $perscomEnlistService->getEnlistmentForm();

        /** @var EnlistmentTopic|null $enlistmentTopic */
        $enlistmentTopic = $enlistmentTopicRepository->findOneBy(['user' => $user], ['submissionId' => 'DESC']);

        if ($enlistmentTopic !== null && $request->get('force_new') === null) {
            $submission = $perscomEnlistService->getCurrentEnlistment($enlistmentTopic->getSubmissionId());
            if (!empty($submission)) {
                return $this->render('@ForumifyPerscomPlugin/frontend/enlistment/enlist_success.html.twig', [
                    'successMessage' => $enlistmentForm['success_message'] ?? '',
                    'enlistmentTopic' => $enlistmentTopic,
                ]);
            }
            // The submission was deleted from PERSCOM.io, continue with the enlistment
        }

        $enlistment = new Enlistment();
        $enlistment->email = $user->getEmail();

        $form = $this->createForm(EnlistmentType::class, $enlistment, ['perscom_form' => $enlistmentForm]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $enlistmentTopic = $perscomEnlistService->enlist($form->getData());

            return $this->render('@ForumifyPerscomPlugin/frontend/enlistment/enlist_success.html.twig', [
                'successMessage' => $enlistmentForm['success_message'] ?? '',
                'enlistmentTopic' => $enlistmentTopic
            ]);
        }

        return $this->render('@ForumifyPerscomPlugin/frontend/enlistment/enlist.html.twig', [
            'form' => $form->createView(),
            'instructions' => $enlistmentForm['instructions'] ?? '',
        ]);
    }
}
