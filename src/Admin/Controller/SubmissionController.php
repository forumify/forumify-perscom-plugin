<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Admin\Form\SubmissionStatusType;
use Forumify\PerscomPlugin\Admin\Service\SubmissionStatusUpdateService;
use Forumify\PerscomPlugin\Perscom\Entity\FormSubmission;
use Forumify\PerscomPlugin\Perscom\Repository\FormRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/submissions', 'submission_')]
#[IsGranted('perscom-io.admin.submissions.view')]
class SubmissionController extends AbstractController
{
    public function __construct(private readonly FormRepository $formRepository)
    {
    }

    #[Route('', 'list')]
    public function list(Request $request): Response
    {
        $formId = $request->get('form');
        $form = $formId !== null ? $this->formRepository->find($formId) : null;

        return $this->render('@ForumifyPerscomPlugin/admin/submissions/list/list.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', 'view')]
    public function view(
        SubmissionStatusUpdateService $submissionStatusUpdateService,
        FormSubmission $submission,
        Request $request
    ): Response {
        $form = $this->createForm(SubmissionStatusType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('perscom-io.admin.submissions.assign_statuses');

            $statusRecord = $form->getData();
            $submissionStatusUpdateService->createStatusRecord($submission, $statusRecord);

            $this->addFlash('success', 'perscom.admin.submissions.view.status_created');
            return $this->redirectToRoute('perscom_admin_submission_view', ['id' => $submission->getId()]);
        }

        return $this->render('@ForumifyPerscomPlugin/admin/submissions/view/view.html.twig', [
            'form' => $form->createView(),
            'submission' => $submission,
        ]);
    }
}
