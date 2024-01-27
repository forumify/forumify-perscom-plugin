<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\PerscomPlugin\Forum\Form\PerscomFormType;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateSubmissionController extends AbstractController
{
    #[Route('/form/{formId}/create-submission', 'form_submission_create')]
    public function __invoke(
        int $formId,
        Request $request,
        PerscomUserService $perscomUserService,
        PerscomFactory $perscomFactory
    ): Response {
        $perscomUser = $perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            throw $this->createAccessDeniedException('You need to have a PERSCOM user to create form submissions');
        }

        try {
            $perscomForm = $perscomFactory->getPerscom()
                ->forms()
                ->get($formId, ['fields'])
                ->json('data');
        } catch (\Exception) {
            throw $this->createNotFoundException("Form with id '$formId' does not exist");
        }

        $form = $this->createForm(PerscomFormType::class, null, ['perscom_form' => $perscomForm]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $perscomFactory->getPerscom()
                ->submissions()
                ->create([
                    'form_id' => $perscomForm['id'],
                    'user_id' => $perscomUser['id'],
                    ...$form->getData()
                ]);

            $this->addFlash('success', 'perscom.opcenter.submission_created');
            return $this->redirectToRoute('perscom_operations_center');
        }

        return $this->render('@ForumifyPerscomPlugin/frontend/form/create_submission.html.twig', [
            'form' => $form->createView(),
            'perscomForm' => $perscomForm,
        ]);
    }
}
