<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function view(PerscomFactory $perscomFactory, int $id): Response
    {
        $submission = $perscomFactory
            ->getPerscom()
            ->submissions()
            ->get($id, ['form', 'form.fields', 'user', 'statuses'])
            ->json('data');

        return $this->render('@ForumifyPerscomPlugin/admin/submissions/view/view.html.twig', [
            'submission' => $submission
        ]);
    }
}
