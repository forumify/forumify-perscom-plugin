<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Admin\Form\StatusRecord;
use Forumify\PerscomPlugin\Admin\Form\StatusRecordType;
use Forumify\PerscomPlugin\Admin\Service\SubmissionStatusUpdateService;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/submissions', 'submission_')]
class SubmissionController extends AbstractController
{
    #[Route('', 'list')]
    public function list(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/admin/submissions/list/list.html.twig');
    }

    #[Route('/{id}', 'view')]
    public function view(
        PerscomFactory $perscomFactory,
        SubmissionStatusUpdateService $submissionStatusUpdateService,
        int $id,
        Request $request
    ): Response {
        $perscom = $perscomFactory->getPerscom();
        $submission = $perscom
            ->submissions()
            ->get($id, ['form', 'form.fields', 'user', 'statuses', 'statuses.record'])
            ->json('data');

        usort($submission['statuses'], static fn ($a, $b) => $b['updated_at'] <=> $a['updated_at']);

        $record = new StatusRecord();
        $record->submission = $submission;
        $form = $this->createForm(StatusRecordType::class, $record);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $statusRecord = $form->getData();
            $submissionStatusUpdateService->createStatusRecord($statusRecord);

            $this->addFlash('success', 'perscom.admin.submissions.view.status_created');
            return $this->redirectToRoute('perscom_admin_submission_view', ['id' => $id]);
        }

        return $this->render('@ForumifyPerscomPlugin/admin/submissions/view/view.html.twig', [
            'submission' => $submission,
            'form' => $form->createView(),
        ]);
    }
}
