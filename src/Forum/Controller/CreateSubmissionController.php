<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\PerscomPlugin\Forum\Form\PerscomFormType;
use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Forumify\PerscomPlugin\Perscom\Entity\FormSubmission;
use Forumify\PerscomPlugin\Perscom\Repository\FormSubmissionRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateSubmissionController extends AbstractController
{
    public function __construct(
        private readonly PerscomUserService $perscomUserService,
        private readonly FormSubmissionRepository $formSubmissionRepository,
    ) {
    }

    #[Route('/form/{id}/create-submission', 'form_submission_create')]
    public function __invoke(Form $perscomForm, Request $request): Response
    {
        $perscomUser = $this->perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            throw $this->createAccessDeniedException('You need to have a PERSCOM user to create form submissions');
        }

        $form = $this->createForm(PerscomFormType::class, null, ['perscomForm' => $perscomForm]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $submission = new FormSubmission();
            $submission->setForm($perscomForm);
            $submission->setUser($perscomUser);
            if ($perscomForm->getDefaultStatus()) {
                $submission->setStatus($perscomForm->getDefaultStatus());
            }
            $submission->setData($form->getData());

            $this->formSubmissionRepository->save($submission);

            $this->addFlash('success', 'perscom.opcenter.submission_created');
            return $this->redirectToRoute('perscom_operations_center');
        }

        return $this->render('@ForumifyPerscomPlugin/frontend/form/create_submission.html.twig', [
            'form' => $form->createView(),
            'perscomForm' => $perscomForm,
        ]);
    }
}
